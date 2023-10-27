create table if not exists ilia_logtable (
	ID int(18) not null auto_increment,
	TIMESTAMP_X varchar(255) not null,
	SECTION_ID varchar(255) not null,
	ELEMENTS_COUNT varchar(50) not null,
    PERSENT varchar(50) not null,
    STATUS varchar(255),
	primary key (ID)
);

create table if not exists ilia_queuetable (
    ID int(18) not null auto_increment,
	SECTION_ID int(18) not null default '0',
    START_COUNT int(18) not null default '0',
    END_COUNT int(18) not null,
    primary key (ID)
);
