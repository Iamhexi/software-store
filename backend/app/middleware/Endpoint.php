<?php

enum Endpoint : string {
    case Auth = 'auth';
    case User = 'user';
    case Software = 'software';
    case Category = 'category';
    case AccountChangeRequest = 'user/{userId}/account_change_request';
    case Review = 'review';
    case Rating = 'rating';
    case BugReport = 'bug_report';
    case StatuteViolationRequest = 'statute_violation_request';
    case SoftwareVersion = 'software_version';
    case Download = 'download';
    case SourceCode = 'source_code';
    case NotExistent = '';

    public static function fromString(string $endpoint): Endpoint {
        switch ($endpoint) {
            case 'auth':
                return self::Auth;
            case 'user':
                return self::User;
            case 'software':
                return self::Software;
            case 'category':
                return self::Category;
            case 'user/{userId}/account_change_request':
                return self::AccountChangeRequest;
            case 'review':
                return self::Review;
            case 'software/{softwareId}/rating/average':
                return self::Rating;
            case 'bug_report':
                return self::BugReport;
            case 'statute_violation_request':
                return self::StatuteViolationRequest;
            case 'software_version':
                return self::SoftwareVersion;
            case 'download':
                return self::Download;
            case 'source_code':
                return self::SourceCode;
            default:
                if (str_contains($endpoint, 'auth'))
                    return self::Auth;
                return self::NotExistent;
        }
    }
}