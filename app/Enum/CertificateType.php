<?php

namespace App\Enum;

enum CertificateType: string
{
    case BARANGAY_CLEARANCE = 'Barangay Clearance';
    case CERTIFICATE_OF_RESIDENCY = 'Certificate of Residency';
    case CERTIFICATE_OF_INDIGENCY = 'Certificate of Indigency';
    case BARANGAY_BUSINESS_CLEARANCE = 'Barangay Business Clearance';
    case CERTIFICATE_OF_NO_OBJECTION = 'Certificate of No Objection';
    case BARANGAY_PROTECTION_ORDER = 'Barangay Protection Order';
    case ENDORSEMENT_LETTER_FOR_HOUSING_PROJECTS = 'Endorsement Letter or Certification for Housing Projects';
}
