<?php

enum RequestStatus: string {
    case Pending = 'Pending';
    case Approved = 'Approved';
    case Declined = 'Declined';
}

function convert_string_to_request_status(string $status): RequestStatus {
    switch ($status) {
        case "Pending":
            return RequestStatus::Pending;
        case "Approved":
            return RequestStatus::Approved;
        case "Declined":
            return RequestStatus::Declined;
        default:
            throw new Exception("Invalid request status: $status");
    }
}