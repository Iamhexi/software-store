<?php
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../SoftwareVersion.php';

class SoftwareVersionRepository {

    private Database $database;

    public function __construct() {
        $this->database = new PDODatabase;
    }

    function find(int $id): ?SoftwareVersion {
        return $this->database->get_rows(
            query: "SELECT * FROM SoftwareVersion WHERE version_id = :version_id;",
            params: ['version_id' => $id],
            class_name: 'SoftwareVersion',
            number: 1
        );
    }

    function findAll(): array {
        return $this->database->get_rows(
            query: "SELECT * FROM SoftwareVersion;",
            class_name: 'SoftwareVersion'
        );
    }

    function save(SoftwareVersion $object): bool {
        return $this->database->execute_query(
            query: "INSERT INTO SoftwareVersion VALUES (:version_id, :software_id, :description, :date_added, :major_version, :minor_version, :patch_version)",
            params: [
                'version_id' => $object->version_id?? NULL,
                'software_id' => $object->software_id,
                'description' => $object->description,
                'date_added' => $object->date_added->format('Y-m-d H:i:s'),
                'major_version' => $object->major_version,
                'minor_version' => $object->minor_version,
                'patch_version' => $object->patch_version
            ]
        );
    }

    function delete(int $id): bool {
        return $this->database->execute_query(
            query: "DELETE SoftwareVersion WHERE version_id = :version_id;",
            params: ['version_id' => $id]
        );
    }

}