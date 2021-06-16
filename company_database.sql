drop table vehicleTypes CASCADE CONSTRAINTS;
drop table vehicles CASCADE CONSTRAINTS;
drop table customers CASCADE CONSTRAINTS;
drop table reservations CASCADE CONSTRAINTS;
drop table rentals CASCADE CONSTRAINTS;
drop table clerks CASCADE CONSTRAINTS;
drop table returns CASCADE CONSTRAINTS;

create table vehicleTypes
	(vtName char(10) not null,
	features char(10) null,
	wrate int null,
	drate int null,
	hrate int null,
	wirate int null,
	dirate int null,
	hirate int null,
	krate int null,
	primary key (vtName));
grant select on vehicleTypes to public;

create table vehicles
	(vlicense char(10) not null,
	make char(10) null,								
	model char(20) null,							
	year int null,
	color char(8) null,
	odometer int null,
	vtName char(10) not null,
	location char(10) not null,
	city char(10) not null,
	status char(10) not null,
	primary key (vlicense),
	foreign key (vtName) references vehicleTypes ON DELETE CASCADE);
grant select on vehicles to public;

create table customers
	(dlicense char(10) not null,
	password int not null,
	cellphone int not null UNIQUE,
	address char(20) null,
	name char(10) not null,
	primary key (dlicense));
grant select on customers to public;

create table reservations
	(confNo char(10) not null,
	vtName char(10) not null,
	vlicense char(10) not null,
	dlicense char(10) not null,
	fromTime timestamp not null,
	toTime timestamp not null,
	primary key (confNo),
	foreign key (vtName) references vehicleTypes,
	foreign key (vlicense) references vehicles ON DELETE CASCADE,
	foreign key (dlicense) references customers ON DELETE CASCADE);
grant select on reservations to public;

create table rentals
	(rid char(10) not null,
	vlicense char(10) not null,
	dlicense char(10) not null,
	fromTime timestamp not null,
	toTime timestamp not null,
	odometer int null,
	cardName char(6) not null,
	cardNo int not null,
	expDate char(10) not null,
	confNo char(10) null,
	primary key (rid),
	foreign key (vlicense) references vehicles ON DELETE CASCADE,
	foreign key (dlicense) references customers ON DELETE CASCADE,
	foreign key (confNo) references reservations ON DELETE CASCADE);
grant select on rentals to public;

create table returns
	(rid char(10) not null,
	retime timestamp not null,
	odometer int not null,
	fulltank char(5) not null,
	value int not null,
	primary key (rid),
	foreign key (rid) references rentals ON DELETE CASCADE);
grant select on returns to public;

create table clerks
	(clid char(10) not null,
	password int not null,
	name char(10) not null,
	primary key (clid));
grant select on clerks to public;


insert into vehicleTypes
	values('full-size', null, 950, 160, 20, 130, 20, 3, 4);

insert into vehicleTypes
	values('economy', null, 800, 120, 13, 100, 18, 2, 2);

insert into vehicleTypes
	values('compact', null, 650, 100, 11, 80, 13, 2, 2);

insert into vehicleTypes
	values('min-size', 'two seats', 500, 70, 8, 60, 9, 1, 1);

insert into vehicleTypes
	values('standard', null, 900, 135, 17, 110, 20, 3, 4);

insert into vehicleTypes
	values('SUV', null, 1200, 190, 25, 150, 23, 4, 5);

insert into vehicleTypes
	values('truck', '5T', 1300, 210, 10, 120, 20, 3, 4);

insert into customers
	values('000888A', 20193041, 7786806010, null, 'GD');

insert into customers
	values('000889A', 20193042, 7786806011, null, 'david');

insert into customers
	values('001224A', 20193043, 7786806020, null, 'gen');

insert into customers
	values('001121A', 20193044, 7783256010, '5960 Bld', 'emily');

insert into customers
	values('001120A', 20193045, 7783256011, '2150 Rd', 'lily');

insert into customers
	values('001122A', 20193046, 7783256012, null, 'kelly');

insert into customers
	values('000819A', 20193047, 7786806015, '6488 LM', 'wency');

insert into customers
	values('000103A', 20193048, 7786800103, '1935 LM', 'gina');

insert into customers
	values('000104A', 20192549, 7786800104, '1935 LM', 'ana');

insert into customers
	values('000105A', 20193025, 7786800105, '1935 MM', 'ena');

insert into customers
	values('000106A', 20193025, 7786800106, '6028 MM', 'ella');

insert into customers
	values('000107A', 20193025, 7786800107, '1512 WM', 'bella');

insert into customers
	values('000108A', 20193025, 7786800108, '2022 EM', 'natalie');

insert into customers
	values('000109A', 20193025, 7786800109, '1512 WM', 'amber');

insert into customers
	values('000110A', 20193025, 7786800110, '6488 LM', 'ada');

insert into customers
	values('000111A', 20193025, 7786800111, '6488 LM', 'anne');

insert into customers
	values('000118A', 20193051, 7786801112, '2235 LM', 'top');

insert into vehicles
	values('KO0804', 'BMW', 'hybrid', 3, 'black', 180, 'standard', 'UT', 'Toronto', 'rented');

insert into vehicles
	values('CAL6N39', 'BMW', 'regular gasoline', 0, 'black', 0, 'full-size', 'UT', 'Toronto','rented');

insert into vehicles
	values('HW722P', 'URUS', 'fully electronic', 1, 'grey', 10, 'min-size', 'UBC', 'GT Van','available');

insert into vehicles
	values('JN33W68', 'URUS', 'fully electronic', 2, 'red', 100, 'full-size', 'SFU', 'GT Van', 'rented');

insert into vehicles
	values('CAA424', 'AUDI', 'regular gasoline', 3, 'silver', 300, 'standard', 'SFU', 'GT Van','reserved');

insert into vehicles
	values('KO8818', 'AUDI', 'regular gasoline', 2, 'black', 180, 'SUV', 'UBC', 'GT Van','available');

insert into vehicles
	values('KO1104', 'PORSCHE', 'hybrid', 0, 'white', 0, 'full-size', 'UBC', 'GT Van','available');

insert into vehicles
	values('TG99SZD', 'PORSCHE', 'regular gasoline', 0, 'blue', 0, 'economy', 'downtown', 'GT Van', 'rented');

insert into vehicles
	values('BJYX99', 'AUDI', 'fully electronic', 1, 'black', 90, 'economy', 'downtown', 'GT Van', 'rented');

insert into vehicles
	values('AWSL233', 'BENZ', 'regular gasoline', 5, 'black', 500, 'standard', 'UT', 'Toronto','available');

insert into vehicles
	values('YJPPLMM', 'BENZ', 'fully electronic', 3, 'white', 150, 'standard', 'UT', 'Toronto', 'rented');

insert into vehicles
	values('ZSWW666', 'BMW', 'fully electronic', 3, 'blue', 180, 'standard', 'downtown', 'GT Van', 'rented');

insert into vehicles
	values('NSDD006', 'BMW', 'regular gasoline', null, 'black', 0, 'economy', 'UBC', 'GT Van','available');

insert into vehicles
	values('KSWL000', 'BMW', 'regular gasoline', 3, 'black', 100, 'economy', 'UBC', 'GT Van','available');

insert into vehicles
	values('NSDD000', 'BMW', 'regular gasoline', null, 'black', 0, 'economy', 'UBC', 'GT Van','rented');

insert into vehicles
	values('NSDD001', 'BMW', 'regular gasoline', null, 'black', 0, 'economy', 'SFU', 'GT Van','rented');

insert into vehicles
	values('NSDD002', 'BMW', 'regular gasoline', null, 'black', 0, 'SUV', 'SFU', 'GT Van','rented');

insert into vehicles
	values('NSDD003', 'BMW', 'fully electronic', null, 'black', 0, 'SUV', 'SFU', 'GT Van','available');

insert into vehicles
	values('NSDD004', 'BMW', 'regular gasoline', null, 'black', 0, 'min-size', 'granville', 'GT Van','available');

insert into vehicles
	values('NSDD005', 'BMW', 'regular gasoline', null, 'silver', 0, 'compact', 'downtown', 'GT Van','available');

insert into vehicles
	values('NSDD007', 'BENZ', 'fully electronic', 3, 'white', 150, 'full-size', 'UT', 'Toronto','reserved');

insert into vehicles
	values('NSDD008', 'BENZ', 'hybrid', 3, 'white', 150, 'economy', 'UT', 'Toronto','available');

insert into vehicles
	values('NSDD009', 'BENZ', 'fully electronic', 3, 'white', 150, 'SUV', 'UT', 'Toronto','available');

insert into vehicles
	values('NSDD010', 'BENZ', 'regular gasoline', 3, 'white', 150, 'economy', 'UT', 'Toronto','available');

insert into vehicles
	values('NSDD011', 'BENZ', 'hybrid', 3, 'white', 150, 'economy', 'downtown', 'Toronto','available');

insert into vehicles
    values('NZND000', 'AUDI', 'fully electronic', 3, 'white', 200, 'full-size', 'UT', 'Toronto','available');

insert into vehicles
    values('NZND001', 'AUDI', 'regular gasoline', 1, 'silver', 150, 'compact', 'UBC', 'GT Van','available');

insert into vehicles
    values('NZND002', 'AUDI', 'fully electronic', 0, 'white', 0, 'standard', 'downtown', 'Toronto','available');

insert into vehicles
    values('NZND003', 'AUDI', 'regular gasoline', 2, 'black', 150, 'economy', 'UT', 'Toronto','available');

insert into vehicles
    values('NZND004', 'AUDI', 'hybrid', 1, 'red', 80, 'economy', 'downtown', 'Toronto','available');

insert into vehicles
    values('NZND005', 'AUDI', 'hybrid', 1, 'black', 280, 'min-size', 'granville', 'GT Van','reserved');

insert into vehicles
    values('NZND006', 'BENZ', 'regular gasoline', 3, 'white', 150, 'economy', 'UT', 'Toronto','available');

insert into vehicles
    values('NZND007', 'BENZ', 'fully electronic', 3, 'white', 150, 'SUV', 'UT', 'Toronto','available');

insert into vehicles
    values('NZND008', 'BENZ', 'regular gasoline', 3, 'white', 150, 'economy', 'downtown', 'GT Van','available');

insert into vehicles
    values('NZND009', 'BENZ', 'hybrid', 3, 'white', 150, 'economy', 'UT', 'Toronto','available');

insert into vehicles
    values('NZND010', 'AUDI', 'fully electronic', 3, 'white', 200, 'full-size', 'granville', 'GT Van','available');

insert into vehicles
    values('NZND011', 'AUDI', 'regular gasoline', 1, 'silver', 150, 'compact', 'UBC', 'GT Van','available');

insert into vehicles
    values('NZND012', 'AUDI', 'fully electronic', 0, 'white', 0, 'standard', 'downtown', 'Toronto','available');

insert into vehicles
    values('NZND013', 'AUDI', 'regular gasoline', 2, 'black', 150, 'economy', 'downtown', 'GT Van','available');

insert into vehicles
    values('NZND014', 'AUDI', 'regular gasoline', 1, 'red', 80, 'economy', 'downtown', 'Toronto','available');

insert into vehicles
    values('NZND015', 'AUDI', 'hybrid', 1, 'black', 280, 'min-size', 'granville', 'GT Van','available');

insert into clerks
	values('C00001', 13579, 'wang');

insert into clerks
	values('C00002', 02468, 'xiao');

insert into reservations
	values('T880818', 'full-size', 'CAL6N39', '000118A', (timestamp '2019-11-28 09:50:00'), (timestamp '2019-12-06 21:50:00'));

insert into reservations
	values('T880820', 'standard', 'CAA424', '000819A', (timestamp '2019-11-28 09:50:00'), (timestamp '2019-12-18 21:50:00'));

insert into reservations
	values('T880821', 'full-size', 'JN33W68', '000889A', (timestamp '2019-11-29 09:50:00'), (timestamp '2019-11-29 21:50:00'));

insert into reservations
	values('G881104', 'standard', 'ZSWW666', '001121A', (timestamp '2019-11-29 09:30:00'), (timestamp '2019-11-29 12:30:00'));

insert into reservations
	values('T880805', 'compact', 'TG99SZD', '001120A', (timestamp '2019-11-29 10:00:00'), (timestamp '2019-11-29 22:00:00'));

insert into reservations
	values('G881106', 'min-size', 'YJPPLMM', '000103A', (timestamp '2019-11-28 10:00:00'), (timestamp '2019-11-29 10:00:00'));

insert into reservations
	values('X881121', 'economy', 'BJYX99', '001224A', (timestamp '2019-11-29 11:00:00'), (timestamp '2019-11-30 11:00:00'));

insert into reservations
	values('T880825', 'standard', 'KO0804', '000888A', (timestamp '2020-01-25 09:00:00'), (timestamp '2020-02-01 09:00:00'));

insert into reservations
	values('T880116', 'economy', 'NSDD000', '000106A', (timestamp '2019-11-28 07:50:00'), (timestamp '2019-11-28 21:50:00'));

insert into reservations
	values('T880216', 'economy', 'NSDD001', '000107A', (timestamp '2019-11-28 21:50:00'), (timestamp '2019-11-29 21:50:00'));

insert into reservations
	values('T880316', 'SUV', 'NSDD002', '000108A', (timestamp '2019-11-29 05:50:00'), (timestamp '2019-11-29 21:50:00'));

insert into reservations
    values('T880317', 'full-size', 'NSDD007', '000108A', (timestamp '2019-11-29 21:50:00'), (timestamp '2019-11-30 21:50:00'));

insert into reservations
    values('T880318', 'min-size', 'NZND005', '001121A', (timestamp '2019-11-28 11:00:00'), (timestamp '2019-11-29 10:00:00'));

insert into reservations
values('E880001', 'economy', 'NZND006', '000889A', (timestamp '2019-11-30 01:50:00'), (timestamp '2019-11-30 21:50:00'));

insert into reservations
values('E880002', 'SUV', 'NZND007', '000819A', (timestamp '2019-11-28 21:50:00'), (timestamp '2019-11-30 21:50:00'));

insert into reservations
values('E880003', 'economy', 'NZND008', '000819A', (timestamp '2019-11-29 21:50:00'), (timestamp '2019-12-10 21:50:00'));

insert into reservations
values('E880004', 'economy', 'NZND009', '000111A', (timestamp '2019-11-30 21:50:00'), (timestamp '2019-12-10 21:50:00'));

insert into reservations
values('E880005', 'full-size', 'NZND010', '000111A', (timestamp '2019-11-30 21:50:00'), (timestamp '2019-12-10 21:50:00'));

insert into reservations
values('E880006', 'compact', 'NZND011', '000819A', (timestamp '2019-11-29 21:50:00'), (timestamp '2019-12-10 21:50:00'));

insert into reservations
values('E880007', 'standard', 'NZND012', '000111A', (timestamp '2019-11-28 21:50:00'), (timestamp '2019-12-10 21:50:00'));

insert into rentals
	values('R880816', 'CAL6N39', '000118A', (timestamp '2019-11-28 21:50:00'), (timestamp '2019-12-02 21:50:00'), 0, 'VISA', '11223347', '2028-05-15', 'T880818');

insert into rentals
	values('R880116', 'NSDD000', '000106A', (timestamp '2019-11-29 21:50:00'), (timestamp '2019-12-25 21:50:00'), 0, 'VISA', '11332251', '2028-05-15', 'T880116');

insert into rentals
	values('R880216', 'NSDD001', '000107A', (timestamp '2019-11-29 21:50:00'), (timestamp '2019-12-05 21:50:00'), 0, 'MASTER', '11332252', '2028-05-15', 'T880216');

insert into rentals
	values('R880316', 'NSDD002', '000108A', (timestamp '2019-11-29 10:50:00'), (timestamp '2019-11-29 21:50:00'), 0, 'MASTER', '11332253', '2028-05-15', 'T880316');

insert into rentals
	values('R880818', 'KO0804', '000888A', (timestamp '2019-11-29 09:00:00'), (timestamp '2020-02-01 09:00:00'), 180, 'VISA', '11223344', '2025-09-01', 'T880825');

insert into rentals
	values('R880819', 'BJYX99', '001224A', (timestamp '2019-11-29 11:00:00'), (timestamp '2019-12-18 11:00:00'), 95, 'VISA', '11223345', '2027-09-01', 'X881121');

insert into rentals
    values('R880821', 'NZND005', '001121A', (timestamp '2019-11-28 11:00:00'), (timestamp '2019-12-28 10:00:00'), 500, 'MASTER', '22668801', '2027-04-19', 'T880318');

insert into rentals
	values('R880822', 'TG99SZD', '001120A', (timestamp '2019-11-30 10:00:00'), (timestamp '2019-11-30 10:00:00'), 0, 'MASTER', '22668803', '2025-08-31', 'T880805');

insert into rentals
	values('R880824', 'ZSWW666', '001121A', (timestamp '2019-11-28 09:30:00'), (timestamp '2019-11-29 09:30:00'), 300, 'MASTER', '22668801', '2027-04-19', 'G881104');

insert into rentals
	values('R880825', 'YJPPLMM', '000103A', (timestamp '2019-11-30 10:00:00'), (timestamp '2019-12-01 10:00:00'), 200, 'VISA', '11668801', '2027-06-01', 'G881106');

insert into rentals
	values('R880826', 'JN33W68', '000889A', (timestamp '2019-11-28 21:50:00'), (timestamp '2019-12-10 21:50:00'), 350, 'MASTER', '22668802', '2026-09-01', 'T880821');

insert into rentals
values('R880827', 'NZND006', '000889A', (timestamp '2019-11-29 21:50:00'), (timestamp '2019-12-10 21:50:00'), 350, 'MASTER', '22668802', '2026-09-01', 'E880001');

insert into rentals
values('R880828', 'NZND007', '000819A', (timestamp '2019-11-29 21:50:00'), (timestamp '2019-12-10 21:50:00'), 10, 'VISA', '22668804', '2026-09-01', 'E880002');

insert into rentals
values('R880829', 'NZND008', '000819A', (timestamp '2019-11-30 21:50:00'), (timestamp '2019-12-10 21:50:00'), 50, 'MASTER', '22668804', '2026-09-01', 'E880003');

insert into rentals
values('R880830', 'NZND009', '000111A', (timestamp '2019-11-30 21:50:00'), (timestamp '2019-12-10 21:50:00'), 30, 'VISA', '22668806', '2026-09-01', 'E880004');

insert into rentals
values('R880831', 'NZND010', '000111A', (timestamp '2019-11-29 21:50:00'), (timestamp '2019-12-10 21:50:00'), 550, 'MASTER', '22668806', '2026-09-01', 'E880005');

insert into rentals
values('R880832', 'NZND011', '000819A', (timestamp '2019-11-29 21:50:00'), (timestamp '2019-12-10 21:50:00'), 150, 'MASTER', '22668804', '2026-09-01', 'E880006');

insert into rentals
values('R880833', 'NZND012', '000111A', (timestamp '2019-11-30 21:50:00'), (timestamp '2019-12-10 21:50:00'), 300, 'MASTER', '22668806', '2026-09-01', 'E880007');

insert into returns
	values('R880816', (timestamp '2019-11-29 21:50:00'), 100, 'yes', 320);

insert into returns
	values('R880818', (timestamp '2020-02-01 09:00:00'), 580, 'yes', 1010);

insert into returns
	values('R880819', (timestamp '2019-11-29 11:00:00'), 200, 'no', 414);

insert into returns
	values('R880821', (timestamp '2019-11-28 10:00:00'), 500, 'no', 900);

insert into returns
	values('R880822', (timestamp '2019-11-28 10:00:00'), 500, 'yes', 1460);

insert into returns
	values('R880824', (timestamp '2019-11-29 09:30:00'), 660, 'no', 2020);

insert into returns
	values('R880825', (timestamp '2019-11-29 10:00:00'), 200, 'yes', 79);

insert into returns
	values('R880826', (timestamp '2019-11-30 21:50:00'), 350, 'no', 1080);

insert into returns
values('R880827', (timestamp '2019-11-29 21:50:00'), 500, 'no', 580);

insert into returns
values('R880828', (timestamp '2019-11-29 21:50:00'), 310, 'yes', 500);

insert into returns
values('R880829', (timestamp '2019-11-29 21:50:00'), 350, 'yes', 580);

insert into returns
values('R880830', (timestamp '2019-11-29 21:50:00'), 360, 'no', 500);

insert into returns
values('R880831', (timestamp '2019-11-29 21:50:00'), 600, 'yes', 580);

insert into returns
values('R880832', (timestamp '2019-11-30 21:50:00'), 200, 'yes', 580);

insert into returns
values('R880833', (timestamp '2019-11-28 21:50:00'), 550, 'yes', 580);


COMMIT;
