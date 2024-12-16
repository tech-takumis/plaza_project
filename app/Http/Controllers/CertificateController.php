<?php

namespace App\Http\Controllers;

use App\Events\DeleteCertificateEvent;
use App\Events\NewCertificateEvent;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{

    public function index()
    {
        $certificate = Certificate::with(['attributes', 'requirements'])->get();

        foreach ($certificate as $cert) {
            if ($cert->template) {
                $cert->template = asset($cert->template);
            }
        }

        return response()->json(['certificate' => $certificate]);
    }

    public function store(Request $request)
    {

        $request->merge([
            'attributes' => array_map(function ($attribute) {
                $attribute['is_required'] = filter_var($attribute['is_required'], FILTER_VALIDATE_BOOLEAN);
                return $attribute;
            }, $request->input('attributes', [])),

            'requirements' => array_map(function ($requirement) {
                $requirement['is_required'] = filter_var($requirement['is_required'], FILTER_VALIDATE_BOOLEAN);
                return $requirement;
            }, $request->input('requirements', [])),
        ]);

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:available,inactive',
            'validity' => 'required|string',
            'template' => 'required|mimes:png,jpg,jpeg,webp,txt,doc,docx,pdf',
            'attributes' => 'array',
            'attributes.*.placeholder' => 'required|string',
            'attributes.*.data_type' => 'required|string|in:text,number,date',
            'attributes.*.is_required' => 'required|boolean',
            'requirements' => 'array',
            'requirements.*.name' => 'required|string',
            'requirements.*.description' => 'nullable|string',
            'requirements.*.datatype' => 'required|string|in:text,number,file',
            'requirements.*.is_required' => 'required|boolean',
        ]);


        $path = null;
        $filename = null;

        if ($request->hasFile('template')) {
            $file = $request->file('template');
            $extension = $file->getClientOriginalExtension();

            $path = 'uploads/certificates/';
            $filename = time() . '.' . $extension;
            $file->move($path, $filename);
        }

        $certificate = Certificate::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'validity' => $request->validity,
            'template' => $path && $filename ? $path . $filename : null,
        ]);

        foreach ($validate['attributes'] ?? [] as $attribute) {
            $certificate->attributes()->create($attribute);
        }

        foreach ($validate['requirements'] ?? [] as $requirement) {
            $certificate->requirements()->create($requirement);
        }

        Broadcast(new NewCertificateEvent($certificate))->toOthers();

        if ($certificate->template) {
            $certificate->template = asset($certificate->template);
        }

        return response()->json(['certificate' => $certificate->load(['attributes', 'requirements'])], 201);
    }

    public function show($id)
    {
        $certificate = Certificate::with(['attributes', 'requirements'])->find($id);

        if (!$certificate) {
            return response()->json(['error' => 'Certificate not found'], 404);
        }

        if ($certificate->template) {
            $certificate->template = asset($certificate->template);
        }

        return response()->json(['certificate' => $certificate]);
    }

    public function update(Request $request, $id)
    {
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return response()->json(['error' => 'Certificate not found'], 404);
        }

        $validate = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:available,inactive',
            'validity' => 'sometimes|string',
            'template' => 'nullable|mimes:png,jpg,jpeg,webp',
            'attributes' => 'array',
            'attributes.*.placeholder' => 'required|string',
            'attributes.*.data_type' => 'required|string|in:text,number,date,file',
            'attributes.*.is_required' => 'required|boolean',
            'requirements' => 'array',
            'requirements.*.name' => 'required|string',
            'requirements.*.description' => 'nullable|string',
            'requirements.*.datatype' => 'required|string|in:text,file',
            'requirements.*.is_required' => 'required|boolean',
        ]);

        $certificate->update($validate);

        if (isset($validate['attributes'])) {
            $certificate->attributes()->delete();
            foreach ($validate['attributes'] as $attribute) {
                $certificate->attributes()->create($attribute);
            }
        }

        if (isset($validate['requirements'])) {
            $certificate->requirements()->delete();
            foreach ($validate['requirements'] as $requirement) {
                $certificate->requirements()->create($requirement);
            }
        }

        return response()->json(['certificate' => $certificate->load(['attributes', 'requirements'])]);
    }

    public function destroy($id)
    {
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return response()->json(['error' => 'Certificate not found'], 404);
        }

        Broadcast(new DeleteCertificateEvent($certificate))->toOthers();

        if (File::exists($certificate->template)) {
            File::delete($certificate->template);
        }

        $certificate->delete();

        return response()->json(['message' => 'Certificate deleted successfully']);
    }
}
