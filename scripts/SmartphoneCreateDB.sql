Create Database Smartphones;
use Smartphones;
create Table Brand(
Id int primary key auto_increment,
Name varchar(20) not null unique,
OS varchar(20) not null,
Status bit default 1);

create table Model(
Id int primary key auto_increment,
Name varchar(20) not null unique,
BrandId int not null,
Battery double not null,
Storage int not null,
DisplaySize double not null,
Chip varchar(30) not null,
Megapixeles int not null,
BioMetrics bit not null,
Comments varchar (200), 
Image varchar(200),
Status bit default 1,

constraint FK_ModelBrand foreign key (BrandId) references Brand(Id)
);

create table Device(
Id int primary key auto_increment,
ModelId int not null unique,
UnitPrice double not null,
Stock int not null, 
Status Bit Default 1,

constraint FK_DeviceModel foreign key (ModelId) references Model(Id)
);

Create Table Sale(
Id int primary key auto_increment,
Datetime datetime default current_timestamp,
DeviceId int not null,
Quantity int not null,
Total double not null,
Status Varchar(50),

constraint FK_SaleDevice foreign key (DeviceId) references Device(Id)
);
