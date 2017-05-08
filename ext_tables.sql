CREATE TABLE tx_imagecompression_domain_model_log (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	ref int(11) unsigned DEFAULT '0' NOT NULL,
	tablename varchar(25) DEFAULT '' NOT NULL,
	compressor varchar(25) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE sys_file_metadata (
	image_compression_status int(1) unsigned DEFAULT '0' NOT NULL
);