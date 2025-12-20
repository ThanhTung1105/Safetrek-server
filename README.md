G. Dự án: "Vệ Sĩ Ảo" (SafeTrek) – Trợ lý Giám sát An toàn Cá nhân
1. Tổng quan Dự án (Project Overview)
"SafeTrek" là một ứng dụng di động hoạt động như một "người bạn đồng hành ảo" hay "vệ sĩ",
được thiết kế để bảo vệ người dùng khi họ đang di chuyển một mình (ví dụ: đi bộ về nhà lúc trời
tối, đi xe ôm/taxi lạ, chạy bộ ở nơi vắng vẻ).
Ứng dụng này không chỉ đơn thuần là chia sẻ vị trí (như "Find My"). Nó là một hệ thống giám sát
chủ động. Người dùng đặt một "hẹn giờ an toàn" cho chuyến đi của mình. Nếu họ không xác
nhận "Tôi đã đến nơi an toàn" trước khi hết giờ, ứng dụng sẽ tự động gửi cảnh báo khẩn cấp (vị
trí cuối cùng, mức pin) đến danh sách liên lạc khẩn cấp đã được cài đặt sẵn
2. Bối cảnh & Vấn đề (Business Problem & Context)
Hiện trạng (Current State): Nỗi sợ hãi khi phải đi một mình ở nơi vắng vẻ hoặc vào ban đêm là
có thật, đặc biệt là với phụ nữ và sinh viên.
1. Sựbất tiện của việc "gọi điện thoại": Nhiều người (đặc biệt là sinh viên nữ) có thói quen
gọi điện thoại cho bạn bè/người thân và giữ máy suốt quãng đường đi bộ về nhà.
o Vấn đề: Việc này rất bất tiện cho cả hai bên, tốn pin, và không phải lúc nào người
nghe cũng rảnh.
2. Phản ứng chậm trễ khi gặp nguy hiểm: Trong tình huống khẩn cấp (bị theo dõi, tấn
công), việc mở điện thoại, tìm danh bạ, gõ tin nhắn hoặc gọi điện là quá chậm và có thể
gây nguy hiểm thêm.
3. Các ứng dụng "Find My" (Tìm bạn) quá bị động: Các ứng dụng như Zalo, Find My
(Apple) chỉ cho phép người khác xem bạn ở đâu (nếu được phép). Chúng không thể biết
được là bạn có đang an toàn hay không. Chúng không tự động cảnh báo nếu có điều gì
đó không ổn.
Cơ hội (Opportunity): Xây dựng một hệ thống "Công tắc Người chết" (Dead Man's Switch) cho
sự an toàn cá nhân. Một ứng dụng "tin cậy" sẽ tự động gọi cứu hộ thay cho bạn nếu bạn không
thể.
3. Đối tượng Người dùng (Target Audience)
Chân dung người dùng (Persona): "Người di chuyển Một mình"
• Mô tả: Sinh viên nữ đi bộ từ trạm xe buýt về phòng trọ lúc 10h tối. Người đi làm tăng ca
bắt taxi về nhà lúc nửa đêm. Người chạy bộ buổi sáng sớm ở công viên vắng.
• Nhu cầu: Cần một "lớp bảo vệ" tự động mà không cần phải chủ động làm gì nhiều.
• Tâm lý: Cảm thấy lo lắng, muốn có ai đó "biết" được hành trình của mình và sẽ hành
động nếu mình gặp chuyện.
4. Yêu cầu Chức năng (Functional Requirements - FRs)
Đây là mô tả chi tiết các tính năng mà hệ thống bắt buộc phải làm được.
FR1: Module "Giám sát Chuyến đi" (Trip Monitoring)
• FR1.1: Bắt đầu Chuyến đi (Hẹn giờ): Người dùng nhập:
o Đích đến (Tùy chọn, để tăng độ chính xác).
o Thời gian dự kiến (Ví dụ: "15 phút").
• FR1.2: Kích hoạt Hẹn giờ: Ứng dụng bắt đầu đếm ngược 15 phút. Trong thời gian này,
ứng dụng sẽ âm thầm theo dõi vị trí GPS của người dùng (background tracking).
• FR1.3: Xác nhận An toàn (Check-in): Khi người dùng đến nơi an toàn (trong vòng 15
phút), họ phải mở app và nhập một mã PIN (hoặc xác thực sinh trắc học) để bấm "Tôi đã
an toàn". Hẹn giờ kết thúc.
FR2: Module "Cảnh báo Khẩn cấp Tự động" (Automatic Alert)
• FR2.1: Kích hoạt Báo động: Nếu bộ hẹn giờ (FR1.2) chạy về 0 mà người dùng không nhập
mã PIN an toàn (FR1.3), ứng dụng sẽ tự động kích hoạt chế độ báo động.
• FR2.2: Gửi Cảnh báo: Hệ thống ngay lập tức gửi tin nhắn (SMS, Push Notification, hoặc
email) đến "Danh bạ Khẩn cấp" (FR5).
• FR2.3: Nội dung Cảnh báo: Tin nhắn phải chứa thông tin quan trọng:
o "Cảnh báo! [Tên Người dùng] đã bắt đầu một chuyến đi lúc [Giờ] và không checkin an toàn."
o "Vị trí cuối cùng được ghi nhận: [Link Google Maps]."
o "Mức pin điện thoại còn lại: [xx%]."
FR3: Module "Nút Hoảng loạn" (Panic Button)
• FR3.1: Kích hoạt Tức thì: Một nút bấm (Widget ngoài màn hình chính hoặc nút lớn trong
app) cho phép người dùng bỏ qua bộ hẹn giờ và gửi cảnh báo (FR2.2) ngay lập tức.
• FR3.2: Kích hoạt Ẩn (Stealth Activation - Tùy chọn): Cho phép kích hoạt bằng cách
bấm nút nguồn 5 lần (hoặc một cử chỉ ẩn khác).
FR4: Module "Mã PIN Bị ép buộc" (Duress PIN)
• FR4.1: Cài đặt PIN Giả: Người dùng cài đặt 2 mã PIN:
o PIN An toàn (ví dụ: 1234)
o PIN Bị ép buộc (ví dụ: 9119)
• FR4.2: Kích hoạt Ngầm: Nếu người dùng bị kẻ tấn công ép buộc phải tắt ứng dụng, người
dùng sẽ nhập "PIN Bị ép buộc" (9119).
o Với kẻ tấn công: Ứng dụng sẽ giả vờ như đã tắt (màn hình hẹn giờ biến mất).
o Ngầm bên dưới: Ứng dụng lập tức gửi cảnh báo khẩn cấp (FR2.2).
FR5: Module "Quản lý Liên lạc Khẩn cấp" (Guardian List)
• FR5.1: Thêm Liên lạc: Người dùng có thể chọn 3-5 người (cha mẹ, bạn thân...) từ danh
bạ làm "Người bảo vệ" (Guardians).
• FR5.2: Yêu cầu Đồng ý: (Quan trọng) Những người này phải chấp nhận lời mời để đảm
bảo họ biết vai trò của mình và đồng ý nhận cảnh báo (tránh spam).
5. Yêu cầu Phi chức năng (Non-Functional Requirements - NFRs)
• NFR1: Độ Tin cậy (Reliability): Quan trọng tuyệt đối. Ứng dụng phải chạy được trong
nền (background service). Cảnh báo (FR2) phải được gửi đi ngay cả khi mạng yếu (ví dụ:
thử gửi SMS nếu Push Notification thất bại).
• NFR2: Tối ưu Pin (Battery Optimization): Việc theo dõi GPS trong nền (FR1.2) không
được làm cạn kiệt pin của người dùng quá nhanh.
• NFR3: Tính Dễ sử dụng (Usability): Thao tác Bắt đầu Chuyến đi (FR1.1) và Kích hoạt Nút
Hoảng loạn (FR3.1) phải cực kỳ nhanh và đơn giản, có thể truy cập ngay từ màn hình
khóa hoặc widget.
• NFR4: Độ Chính xác Vị trí: GPS phải có độ chính xác cao nhất có thể
6. Ràng buộc & Giả định (Constraints & Assumptions)
• Ràng buộc 1 (Lớn nhất): Ứng dụng phụ thuộc rất nhiều vào Quyền của Hệ điều hành
(GPS, Chạy nền, Gửi SMS). Trên các HĐH (như iOS hoặc một số Android của Trung Quốc)
"bóp" ứng dụng chạy nền rất chặt, đây là một thách thức kỹ thuật lớn.
• Ràng buộc 2: Ứng dụng này không thay thế các dịch vụ khẩn cấp (như gọi 113). Nó là
một công cụ thông báo cho người thân.
• Giả định 1: Người dùng có smartphone với GPS và có kết nối dữ liệu di động hoặc SMS.
• Giả định 2: "Người bảo vệ" (FR5) là những người đáng tin cậy, sẽ kiểm tra thông báo và
hành động khi nhận được cảnh báo.