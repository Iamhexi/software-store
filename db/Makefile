test: insert_into_download

insert_into_download:
	mariadb \
	--defaults-file=connection_configuration.cnf \
	-D software_store \
	-e \
	"INSERT INTO Download (download_id, user_id, executable_id, date_download) VALUES (NULL, 1, 1, '2023-11-12');" \
	--tee=test_results.txt