create table if not exists f_product(
	product_id integer not null auto_increment,
	category1 varchar(255),
	category2 varchar(255),
	name varchar(1024),
	belong varchar(255),
	maxlimit double,
	minlimit double,
	maxrate double,
	minrate double,
	maxperiod integer,
	minperiod integer,
	periodstep integer,
	repayment varchar(255),
	maxage integer,
	minage integer,
	template_id integer,
	material text,
	remark text,
	category_img varchar(1024),
	product_img varchar(1024),
	PRIMARY KEY (product_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists f_product_exclude(
	product_id integer not null,
	exclude_id integer not null,
	PRIMARY KEY (product_id, exclude_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists f_require(
	require_id integer not null auto_increment,
	require_group integer not null,
	class_id integer not null,
	name varchar(2048),
	remark text default null,
	PRIMARY KEY (require_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists f_require_value(
	rvalue_id integer not null auto_increment,
	value_type varchar(16) not null,
	require_group integer not null,
	rvalue varchar(1024) default null,
	PRIMARY KEY (rvalue_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table if not exists f_product_require(
	product_id integer not null,
	require_id integer not null,
	value_set varchar(64),
	expresion varchar(255),
	PRIMARY KEY (product_id, require_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE if not exists f_customer_require (
	customer_id int(11) NOT NULL,
	require_id int(11) NOT NULL,
	rvalue_id int(11) NOT NULL,
	rvalue varchar(1024) default null,
	PRIMARY KEY  (customer_id, require_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE if not exists f_customer_product (
	customer_id int(11) NOT NULL,
	product_id int(11) NOT NULL,
	matching int(11) NOT NULL,
	PRIMARY KEY  (customer_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE if not exists f_product_step (
	product_id int(11) NOT NULL,
	step_id int(11) NOT NULL,
	step_name varchar(1024),
	step_text text,
	PRIMARY KEY (product_id, step_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE if not exists f_product_material (
	material_id int(11) NOT NULL auto_increment,
	product_id int(11) NOT NULL,
	step_id int(11) default 0, 							--材料属于哪一步
	m_name varchar(1024),
	m_text text,
	PRIMARY KEY (material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE if not exists f_application (
	app_id int(11) NOT NULL auto_increment,
	product_id int(11) not null,
	customer_id int(11) not null,
	user_id int(11) default 0,						--接单人员ID
	app_status int(11) default 0,					--0未处理，1处理中，2已完成，3未通过
	cur_step_id int(11) default 0,					--当前步骤
	step_status int(11) default 0,					--0未处理，1处理中，2通过，3中止
	date_added timestamp,
	date_over timestamp,
	rst_amount varchar(255),
	rst_period varchar(255),
	rst_rate varchar(255),
	PRIMARY KEY (app_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE if not exists f_app_material (
	app_id int(11) NOT NULL,
	material_id int(11) NOT NULL,						 -- 0 表示临时补充上传的资料
	m_filename varchar(1024),
	PRIMARY KEY (app_id, material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE if not exists f_app_message (
	msg_id int(11) not null,
	app_id int(11) not null,
	step_id int(11) not null,
	date_added timestamp,
	msg text,
	PRIMARY KEY (msg_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*
CREATE TABLE if not exists f_app_step (
	app_id int(11) NOT NULL,
	step_id int(11) not null,
	step_status int(11),								--1处理中，2通过，3中止
	date_added timestamp,
	PRIMARY KEY  (app_id, step_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

/* 客户与系统帐户之间的关联 1:n
 * 系统帐户包括销售，门店，各级上级代理
 */
alter table oc_customer add user_id int(11) default 0;
alter table oc_user add user_pid int(11) default 0;
alter table oc_user add telephone varchar(32);
alter table oc_user add constraint uk_user_name unique (username);

/*维护SQL*/
select customer_id,email,telephone,user_id,salt,password from oc_customer;
select user_id,username,firstname,user_pid,usertype,user_group_id,salt,password from oc_user;
select usertype,count(*) from oc_invitecode group by usertype;
/*查询单纯的客户*/
select customer_id,email,telephone,user_id,date_added,salt,password from oc_customer where telephone not in (select username from oc_user);
/*查询某帐号下的代理商和业务员*/
select user_id,username,firstname,user_pid,usertype,user_group_id,date_added,(select count(*) from oc_user where user_pid = u.user_id and usertype > 0) as user_cnt, (select count(*) from oc_customer where user_id=u.user_id and telephone not in (select username from oc_user)) as customer_cnt from oc_user u where user_pid = (select user_id from oc_user where username='18602875365') and usertype > 0 order by usertype desc;
/*查询某帐号下的客户*/
select customer_id,email,telephone,user_id,date_added,salt,password from oc_customer where user_id=(select user_id from oc_user where username='18683521881') and telephone not in (select username from oc_user);
/*把客户帐号变成业务员或代理商*/
insert into oc_user(user_group_id,username,password,salt,firstname,lastname,email,code,ip,status,date_added,district_id,usertype,user_pid) 
select 10,telephone,password,salt,firstname,lastname,email,'','',1,date_added,0,2,10 from oc_customer where customer_id in (26,29)
/*设置分类和产品图标*/
update f_product set product_img='data/products.jpg';
update f_product set category_img='data/categories2.jpg';
update f_product set category_img='data/pcar.jpg' where category2='车信贷';
update f_product set category_img='data/pcar2.jpg' where category2='车抵押贷';
update f_product set category_img='data/phouse2.jpg' where category2='房信贷';
update f_product set category_img='data/phouse.jpg' where category2='房产抵押贷';
