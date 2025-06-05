#Account login Admin
Admin:    admin@sennet.vn /   Admin1122
User:     user@sennet.vn /   Admin1122

#Các hàm, biến
- Exit câu thông báo: exitError('Nội dung');
- Generate đoạn param thickbox: param_thickbox(width = 1000, height = 600);

#Company làm việc
- Tất cả các User khi sử dụng hệ thống đều phải thuộc ít nhất 1 cty
- Ở môi trường User, tại 1 thời điểm thì 1 User chỉ được sử dụng các tính năng của 1 công ty, sẽ phải chọn ở trang /home.php
- Có 2 constant được define để sử dụng: ACCOUNT_ID và COMPANY_ID

#Công ty, Phòng/Ban, User
- 1 company khi tạo sẽ mặc định có 2 department: CEO và Tổng hợp
- CEO có field is_boss = 1, Tổng hợp có field is_default = 1
- Khi gán 1 user vào company thì mặc định sẽ gán vào Tổng hợp, và luôn tồn tại ở department Tổng hợp, chỉ khi nào xóa User khỏi cty thì mới bị xóa khỏi department Tổng hợp

#Giới hạn Data công ty
Tất cả các truy vấn liên quan đến các bảng dữ liệu riêng của cty đều phải gọi đến hàm generate SQL:
$sql_where  =   sql_company(prefix_field);

#Docker setup
docker compose -f docker-compose.yml exec php-fpm composer install
docker compose -f docker-compose.yml exec php-fpm sh -c "cd database; composer install"
docker compose -f docker-compose.yml exec php-fpm sh -c "cd database; vendor/bin/phinx migrate"