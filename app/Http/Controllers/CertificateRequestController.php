<?php

namespace App\Http\Controllers;

use App\Events\CertificateRequestEvent;
use App\Models\User;
use App\Models\UserAction;
use App\Models\Certificate;
use InvalidArgumentException;
use App\Mail\CertificateApproved;
use App\Models\CertificateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\CertificateRequirements;
use Illuminate\Http\Request;
use App\Models\CertificateRequestAttribute;
use App\Models\CertificateRequestRequirements;
use Illuminate\Support\Facades\Broadcast;

class CertificateRequestController extends Controller
{

    public function createRequest(Request $request)
    {
        $user = Auth::user();

        // Use $request->validate() instead of Request::validate()
        $validated = $request->validate([
            'certificate_id' => 'required|string|exists:certificates,id',
        ]);

        $certificate = Certificate::with(['attributes', 'requirements'])->findOrFail($validated['certificate_id']);

        $validAttributeKeys = $certificate->attributes->map(fn($attr) => 'attributes_' . $attr->id)->toArray();
        $validRequirementKeys = $certificate->requirements->map(fn($req) => 'requirements_' . $req->id)->toArray();

        // Filter the request data to only include valid attributes and requirements
        $attributes = array_filter($request->all(), fn($key) => in_array($key, $validAttributeKeys), ARRAY_FILTER_USE_KEY);
        $requirements = array_filter($request->all(), fn($key) => in_array($key, $validRequirementKeys), ARRAY_FILTER_USE_KEY);

        $dynamicRules = [];

        // Add certificate_id validation to dynamic rules
        $dynamicRules['certificate_id'] = 'required|string|exists:certificates,id';

        // Build validation rules dynamically based on data_type
        foreach ($certificate->attributes as $attribute) {
            $key = "attributes_{$attribute->id}";
            switch ($attribute->data_type) {
                case 'string':
                    $dynamicRules[$key] = 'string';
                    break;
                case 'number':
                    $dynamicRules[$key] = 'integer';
                    break;
                case 'text':
                    $dynamicRules[$key] = 'string';
                    break;
                case 'date':
                    $dynamicRules[$key] = 'string';
                    break;
                case 'file':
                    $dynamicRules[$key] = 'required|file|mimes:png,jpg,jpeg,webp,txt,doc,docx,pdf';
                    break;
                default:
                    throw new InvalidArgumentException("Unsupported data type: {$attribute->data_type}");
            }
        }

        foreach ($certificate->requirements as $requirement) {
            $key = "requirements_{$requirement->id}";
            switch ($requirement->datatype) {
                case 'string':
                    $dynamicRules[$key] = 'string';
                    break;
                case 'date':
                    $dynamicRules[$key] = 'string';
                    break;
                case 'integer':
                    $dynamicRules[$key] = 'integer';
                    break;
                case 'number':
                    $dynamicRules[$key] = 'integer';
                    break;
                case 'text':
                    $dynamicRules[$key] = 'string';
                    break;
                case 'file':
                    $dynamicRules[$key] = 'file|mimes:png,jpg,jpeg,webp,txt,doc,docx,pdf';
                    break;
                default:
                    throw new InvalidArgumentException("Unsupported data type: {$requirement->datatype}");
            }
        }

        // Validate against dynamic rules using $request->validate()
        $validated = $request->validate($dynamicRules);

        DB::beginTransaction();

        try {
            // Create the certificate request
            $requestRecord = CertificateRequest::create([
                'user_id' => $user->id,
                'certificate_id' => $validated['certificate_id'],
                'status' => 'pending',
            ]);

            // Save attributes
            foreach ($validated as $key => $value) {
                if (str_starts_with($key, 'attributes_')) {
                    // Match by ID from the key
                    $attribute = $certificate->attributes->firstWhere('id', str_replace('attributes_', '', $key));

                    if ($attribute) {
                        CertificateRequestAttribute::create([
                            'certificate_request_id' => $requestRecord->id,
                            'attribute_name' => $attribute->placeholder,
                            'attribute_value' => $value,
                        ]);
                    }
                }
            }

            // Save requirements
            foreach ($validated as $key => $value) {
                if (str_starts_with($key, 'requirements_')) {
                    $requirement = $certificate->requirements->firstWhere('id', str_replace('requirements_', '', $key)); // Match by ID from key

                    if ($requirement) {
                        if ($requirement->datatype === 'file' && $request->hasFile($key)) {
                            $file = $request->file($key);
                            $path = 'uploads/certificates/credentials';
                            $absolutePath = public_path($path);

                            if (!file_exists($absolutePath)) {
                                mkdir($absolutePath, 0777, true);
                            }

                            $filename = time() . '_' . $file->getClientOriginalName();
                            $file->move($absolutePath, $filename);

                            CertificateRequestRequirements::create([
                                'certificate_request_id' => $requestRecord->id,
                                'requirement_name' => $requirement->name,
                                'requirement_value' => "{$path}/{$filename}",
                                'certificate_requirement_id' => str_replace('requirements_', '', $key),
                            ]);
                        } else {
                            CertificateRequestRequirements::create([
                                'certificate_request_id' => $requestRecord->id,
                                'requirement_name' => $requirement->name,
                                'requirement_value' => $value,
                                'certificate_requirement_id' => str_replace('requirements_', '', $key),
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            Broadcast(new CertificateRequestEvent($requestRecord))->toOthers();

            UserAction::create([
                'user_id' => $user->id, // Ensure user_id is passed
                'action_type' => 'request_certificate', // Specify the action type
                'ip_address' => $request->ip(), // Capture the IP address from the request
                'device_info' => $request->header('User-Agent'), // Optionally capture the device info (optional)
                'action_timestamp' => now(), // Use the current timestamp for the action
            ]);


            return response()->json([
                'message' => 'Certificate request submitted successfully.',
                'request' => $requestRecord->load('attributeValues', 'requirementValues'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error during certificate request: {$e->getMessage()}");
            return response()->json(['message' => 'An error occurred while processing your request.'], 500);
        }
    }



    // View requests submitted by the authenticated user
    public function userCertificateRequst()
    {
        $userAuth = Auth::user();
        $requests = CertificateRequest::with(['attributeValues', 'requirementValues'])->where('user_id', $userAuth->id)->get();
        // $requests = CertificateRequest::with(['attributeValues', 'requirementValues'])->get();


        foreach ($requests as $req) {
            if ($req->requirement_value) {
                $req->requirement_value = asset($req->requirement_value);
            }
        }

        return response()->json($requests);
    }

    // Show details of a specific certificate request
    public function show($id)
    {
        $request = CertificateRequest::with(['certificate', 'attributes', 'requirements'])->findOrFail($id);

        return response()->json($request);
    }

    public function index()
    {
        $requests = CertificateRequest::with(['attributeValues', 'requirementValues'])->get();

        foreach ($requests as $request) {
            foreach ($request->requirementValues as $requirementValue) {
                // Assuming 'certificate_requirements' is the table where the data_type is stored
                $certificateRequirement = CertificateRequirements::find($requirementValue->certificate_requirement_id);

                if ($certificateRequirement && $certificateRequirement->datatype === 'file') {
                    $requirementValue->requirement_value = asset($requirementValue->requirement_value);
                }
            }
        }

        return response()->json($requests);
    }

    public function approveCertificate(Request $request, $id)
    {

        $request->validate([
            'document_file' => 'required|mimes:png,jpg,jpeg,webp,txt,doc,docx,pdf',
        ]);

        // Handle file upload
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');

            // Store the file
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = '/uploads/approved';
            $file->move(public_path($path), $filename);

            // Update the database record
            $certificateRequest = CertificateRequest::find($id);
            if ($certificateRequest) {
                $certificateRequest->status = 'approved';
                $certificateRequest->save();
            }

            // Get the user associated with the request
            $user = User::find($certificateRequest->user_id);

        // Send the email with attachment
            $filePath = public_path($path . '/' . $filename);
            Mail::to($user->email)->send(new CertificateApproved($user, $filePath));
        } else {
            Log::error('No file found in the request.');
            return response()->json(['error' => 'File upload failed'], 422);
        }

        return response()->json(['message' => 'File uploaded and record updated successfully']);
    }



    public function update(Request $request, $id)
    {

        $user = Auth::user();
        // Fetch the certificate request
        $certificateRequest = CertificateRequest::findOrFail($id);

        // Validate incoming data
        $validated = $request->validate([
            'certificate_id' => 'sometimes|string',
            'status' => 'sometimes|in:pending,approved,rejected',
            'attributes' => 'sometimes|array',
            'requirements' => 'sometimes|array',
        ]);

        DB::beginTransaction();

        try {
            // Update certificate request details
            if (isset($validated['certificate_id'])) {
                $certificateRequest->certificate_id = $validated['certificate_id'];
            }
            if (isset($validated['status'])) {
                $certificateRequest->status = $validated['status'];
            }
            $certificateRequest->save();

            // Update attributes if provided
            if (isset($validated['attributes'])) {
                foreach ($validated['attributes'] as $attributeId => $value) {
                    $attribute = $certificateRequest->attributes()->where('id', $attributeId)->first();
                    if ($attribute) {
                        $attribute->update(['attribute_value' => $value]);
                    }
                }
            }

            // Update requirements if provided
            if (isset($validated['requirements'])) {
                foreach ($validated['requirements'] as $requirementId => $value) {
                    $requirement = $certificateRequest->requirements()->where('id', $requirementId)->first();
                    if ($requirement) {
                        $requirement->update(['requirement_value' => $value]);
                    }
                }
            }

            DB::commit();


            UserAction::create([
                'user_id' => $user->id,
                'action_type' => 'update_certificate_request',
                'ip_address' => $request->ip(),
                'device_info' => $request->header('User-Agent'),
                'action_timestamp' => now(),
            ]);

            return response()->json(['message' => 'Certificate request updated successfully.', 'request' => $certificateRequest]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating certificate request: {$e->getMessage()}");
            return response()->json(['message' => 'An error occurred while updating the certificate request.'], 500);
        }
    }
    public function destroy(Request $request,$id)
    {
        $userAuth = Auth::user();
        DB::beginTransaction();

        try {

            //Try to fetch the certificate request if it does not exist it will throw an error.
            $certificateRequest = CertificateRequest::find($id);
            if(!$certificateRequest) {
                Log::error("Error deleting certificate request: Certificate request with ID {$id} does not exist.");
                return response()->json(['message' => 'Certificate request not found.'], 404);
            }


            // Delete associated attributes and requirements
            $certificateRequest->attributeValues()->delete();
            $certificateRequest->requirementValues()->delete();

            // Delete the certificate request
            $certificateRequest->delete();

            DB::commit();

            UserAction::create([
                'user_id' => $userAuth->id,
                'action_type' => 'delete_certificate_request',
                'ip_address' => $request->ip(),
                'device_info' => $request->header('User-Agent'),
                'action_timestamp' => now(),
            ]);


            return response()->json(['message' => 'Certificate request deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting certificate request: {$e->getMessage()}");
            return response()->json(['message' => 'An error occurred while deleting the certificate request.'], 500);
        }
    }
}
