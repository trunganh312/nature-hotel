import dayjs from '@lib/dayjs';
export default {
    getPrice(value) {
        value = _.toNumber(`${value}`.split(',').join(''));
        return isNaN(value) ? 0 : value;
    },
    // Hàm tính toán khoảng thời gian giữa hai ngày
    calculateDateDifference(startDate, endDate) {
        // Tạo đối tượng Date từ chuỗi ngày bắt đầu và kết thúc
        var start = new Date(startDate);
        var end = new Date(endDate);

        // Tính toán số mili giây giữa hai ngày
        var difference = end.getTime() - start.getTime();

        // Chuyển đổi sang ngày, giờ, phút, giây
        var millisecondsInADay = 1000 * 60 * 60 * 24;
        var daysDifference = Math.floor(difference / millisecondsInADay);

        // Trả về số ngày chênh lệch
        return daysDifference;
    },
    //Format number 1234567.89 to 1,234,567.89
    formatNumber(n) {
        if (n == 0) return 0;
        if (!n) return '';
        if (n == '-') return n;

        var number = n.toString().replace(/,/g, '');
        number = Number(number);

        if (isNaN(number)) return '';

        var parts = number.toString().split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        return parts.join('.');
    },
    inputNumberFormatter(value) {
        return `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    },
    formatCurrency(value) {
        return this.inputNumberFormatter(value);
    },
    inputNumberParser(value) {
        value = String(value);
        return value.replace(/\$\s?|(,*)/g, '');
    },
    filterOption(input, option) {
        return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
    },
    objectToFormData(obj) {
        let formData = new FormData();
        for (const key in obj) {
            if (obj.hasOwnProperty(key)) {
                formData.append(key, obj[key]);
            }
        }

        return formData;
    },
    // Hàm trả về thứ của một ngày
    getDayOfWeek(dateString) {
        var date = new Date(dateString);
        var dayIndex = date.getDay() - 1;
        dayIndex = dayIndex < 0 ? 6 : dayIndex;
        return dayIndex;
    },
    // Date string to ISO 8601
    parseDate(dateString) {
        let parts = dateString.split('/');
        // parts[2] = year, parts[1] = month (zero-based index), parts[0] = day
        return new Date(parts[2], parts[1] - 1, parts[0]);
    },
    // Conver daterange sang dạng ngày/tháng/năm - ngày/tháng/năm đề phục vụ việc query
    formatDateRange(daterange) {
        const start = dayjs(daterange[0], 'DD/MM/YYYY');
        const end = dayjs(daterange[1], 'DD/MM/YYYY');
        const formattedStart = start.format('DD/MM/YYYY');
        const formattedEnd = end.format('DD/MM/YYYY');
        return `${formattedStart}-${formattedEnd}`;
    },
    // Dùng cho input number ant
    formatter(value) {
        if (!value) return '0';
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    },
    parser(value) {
        return value.replace(/\$\s?|(,*)/g, '');
    },
    // Chuyển timestamp sang ngày,tháng,năm, giờ phút giây
    formatTimestamp(timestamp, returnDateOnly = false) {
        // Chuyển timestamp từ chuỗi sang số nguyên và nhân với 1000 để đổi từ giây sang mili-giây
        const date = new Date(parseInt(timestamp) * 1000);

        const options = returnDateOnly
            ? { day: '2-digit', month: '2-digit', year: 'numeric' } // Nếu chỉ muốn trả về ngày/tháng/năm
            : { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false }; // Nếu muốn trả về cả ngày giờ

        // Định dạng ngày theo locale Việt Nam
        const formattedDate = date.toLocaleString('vi-VN', options).split(' ');

        if (returnDateOnly) {
            return formattedDate[0];
        } else {
            return `${formattedDate[1]} ${formattedDate[0]}`;
        }
    },
    // Tạo uid
    generateUID() {
        // I generate the UID from two parts here
        // to ensure the random number provide enough bits.
        var firstPart = (Math.random() * 46656) | 0;
        var secondPart = (Math.random() * 46656) | 0;
        firstPart = ('000' + firstPart.toString(36)).slice(-3);
        secondPart = ('000' + secondPart.toString(36)).slice(-3);
        return firstPart + secondPart;
    },
    // Chuyển slug
    toSlug(slug) {
        //Đổi ký tự có dấu thành không dấu
        slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
        slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
        slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
        slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
        slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
        slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
        slug = slug.replace(/đ/gi, 'd');
        //Xóa các ký tự đặt biệt
        slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi, '');
        //Đổi khoảng trắng thành ký tự gạch ngang
        slug = slug.replace(/ /gi, '-');
        //Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
        //Phòng trường hợp người nhập vào quá nhiều ký tự trắng
        slug = slug.replace(/\-\-\-\-\-/gi, '-');
        slug = slug.replace(/\-\-\-\-/gi, '-');
        slug = slug.replace(/\-\-\-/gi, '-');
        slug = slug.replace(/\-\-/gi, '-');
        //Xóa các ký tự gạch ngang ở đầu và cuối
        slug = '@' + slug + '@';
        slug = slug.replace(/\@\-|\-\@|\@/gi, '');
        return slug.toLowerCase();
    }
};
