<?php

enum RequestStatus: string {
    case Pending = 'Pending';
    case Approved = 'Approved';
    case Declined = 'Declined';

    public static function convert_string_to_request_status(string $status): RequestStatus {
        switch ($status) {
            case "Pending":
            case "pending":
                return RequestStatus::Pending;
            case "Approved":
            case "approved":
                return RequestStatus::Approved;
            case "Declined":
            case "declined":
                return RequestStatus::Declined;
            default:
                throw new Exception("Invalid request status: $status");
        }
    }
}