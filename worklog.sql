create table user(
    id varchar(20) NOT NULL PRIMARY KEY,
    password varchar(20) NOT NULL,
    name varchar(20) NOT NULL,
    is_work varchar(10) NOT NULL,
    start_time varchar(20)
);

create table log(
    id int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id varchar(20) NOT NULL,
    start_time varchar(20) NOT NULL,
    end_time varchar(20) NOT NULL,
    sub_min varchar(20) NOT NULL
);