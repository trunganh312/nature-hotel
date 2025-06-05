#SENNET Migration

## Create migration
Tên của migration viết theo chuẩn Camel Case.
Cấu trúc tên [Action][Object] -> [Create/Alter/...][Table/Col/Index/...]
vd: CreateTableUser, CreateColPhone, AlterTableUser, AlterColPhone
* Với các trường hợp bị trùng tên thì thêm stt vào cuối như sau: AlterTableUser -> AlterTableUser01

### CLI create 
vendor/bin/phinx create [MigrationName]

### CLI run migrate all
vendor/bin/phinx migrate

### CLI run migrate version
vendor/bin/phinx migrate -t [MigrationVersion]
* Sẽ chạy migration version được chỉ định và kèm theo tất cả các migration version nhỏ hơn migration version chỉ định

### CLI run rollback version
vendor/bin/phinx rollback -t [MigrationVersion]
* Sẽ rollback tất cả các migration version lớn hơn migration version chỉ định

### CLI run rollback version all
vendor/bin/phinx rollback -t 0

* MigrationVersion: Thông tin phiên bản của migration (20231111042216_create_table_wards.php -> CreateTableWards)
* MigrationVersion: Thông tin phiên bản của migration (20231111042216_create_table_wards.php -> 20231111042216)

## Seeder
Module chạy sql để tạo dữ liệu mặc định vào db
Tên của seeder viết theo chuẩn Camel Case.
Cấu trúc tên [Object]Seeder -> [Table]Seeder
vd: UserSeeder
* Với các trường hợp bị trùng tên thì thêm stt vào cuối như sau: UserSeeder -> UserSeeder01

### CLI create 
vendor/bin/phinx seed:create [SeederName]

### CLI run seeder
vendor/bin/phinx seed:run

### CLI run seeder
vendor/bin/phinx seed:run -s [SeederName]