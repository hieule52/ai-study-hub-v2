CREATE DATABASE  IF NOT EXISTS `aistudyhublms` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `aistudyhublms`;
-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: aistudyhublms
-- ------------------------------------------------------
-- Server version	9.5.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '6c4b2c32-be4f-11f0-b103-d843ae2bcf25:1-761';

--
-- Table structure for table `ai_messages`
--

DROP TABLE IF EXISTS `ai_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `message` text,
  `response` text,
  `is_important` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_ai_messages_user` (`user_id`),
  CONSTRAINT `ai_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_messages`
--

LOCK TABLES `ai_messages` WRITE;
/*!40000 ALTER TABLE `ai_messages` DISABLE KEYS */;
INSERT INTO `ai_messages` VALUES (2,8,'Java là gì?','Java là ngôn ngữ lập trình hướng đối tượng',0,'2026-04-17 03:06:51'),(3,8,'Tạo cho mình một bài tập Python cơ bản để luyện vòng lặp For.','Xin chào! Tôi rất hân hạnh được giúp đỡ bạn trong việc luyện tập vòng lặp For bằng Python.\n\nVòng lặp For là một trong những kỹ thuật cơ bản trong lập trình Python, giúp bạn lặp lại một khối mã nhiều lần. Dưới đây là một bài tập cơ bản để bạn luyện tập:\n\n**Bài tập:** In ra màn hình các số từ 1 đến 10.\n\n**Mô tả:** Viết một chương trình Python sử dụng vòng lặp For để in ra các số từ 1 đến 10 trên màn hình.\n\n**Giải thích:**\n\n1. Sử dụng `range()` để tạo một danh sách các số từ 1 đến 10.\n2. Sử dụng vòng lặp For để lặp qua danh sách các số.\n3. Trong mỗi lần lặp, in ra số hiện tại.\n\n**Mã code:**\n```python\n# Tạo một danh sách các số từ 1 đến 10\nsố = range(1, 11)\n\n# Sử dụng vòng lặp For để lặp qua danh sách các số\nfor số in số:\n    # In ra số hiện tại\n    print(số)\n```\n**Lời giải:**\n\nKhi bạn chạy chương trình trên, nó sẽ in ra màn hình các số từ 1 đến 10.\n\n**Hãy thử:**\n\n- Ghi lại mã code trên vào một tệp tin Python (.py).\n- Chạy chương trình bằng cách sử dụng Python (ví dụ: `python tên_tệp_tin.py`).\n- Theo dõi kết quả in ra màn hình.\n\n**Lưu ý:**\n\n- Bạn có thể thay đổi số lượng số mà chương trình in ra bằng cách thay đổi tham số trong `range()`.\n- Bạn có thể thêm các lệnh khác vào vòng lặp For để thực hiện các hành động khác nhau.\n\nChúc mừng bạn đã hoàn thành bài tập này! Bạn đã luyện tập thành công vòng lặp For bằng Python.',0,'2026-04-23 17:58:05'),(4,8,'chào','Xin chào! Bạn đã đến với AI Study Hub LMS rồi! Tôi rất hân hạnh được giúp đỡ bạn trên hành trình học tập và kiến thức mới. Bạn cần giúp đỡ điều gì hôm nay?',0,'2026-04-23 17:58:13'),(5,8,'định lý pytago','Xin chào học sinh!\n\nTôi biết bạn đang tìm hiểu về định lý Pythagor, một khái niệm toán học cơ bản nhưng rất quan trọng. Định lý Pythagor là một trong những định lý quan trọng nhất trong hình học, và nó được sử dụng rộng rãi trong nhiều lĩnh vực khác nhau.\n\n**Định lý Pythagor:**\n\n\"Trong một tam giác vuông, bình phương của độ dài hai đường bên (cạnh huyền và cạnh kề) bằng bình phương của độ dài cạnh huyền.\"\n\nHay nói một cách đơn giản hơn, định lý Pythagor cho biết rằng:\n\na^2 + b^2 = c^2\n\nTrong đó:\n\n- a và b là độ dài hai đường bên (cạnh huyền và cạnh kề)\n- c là độ dài cạnh huyền (đường bên dài nhất)\n\n**Ví dụ:**\n\nXin xem xét một tam giác vuông có độ dài hai đường bên là 3 và 4, và độ dài cạnh huyền là 5. Theo định lý Pythagor, ta có:\n\n3^2 + 4^2 = 5^2\n9 + 16 = 25\n25 = 25\n\nVậy, định lý Pythagor đúng!\n\n**Ứng dụng:**\n\nĐịnh lý Pythagor có rất nhiều ứng dụng trong thực tế, như:\n\n- Tính toán độ dài đường thẳng trong một tam giác vuông\n- Tính toán diện tích của một hình vuông\n- Tính toán khoảng cách giữa hai điểm trong không gian\n\nHy vọng những thông tin này sẽ giúp bạn hiểu rõ hơn về định lý Pythagor. Nếu bạn có bất kỳ câu hỏi nào khác, hãy đừng ngần ngại hỏi tôi!\n\nChúc bạn học tập tốt!',0,'2026-04-23 17:58:31'),(6,8,'Làm sao để sửa lỗi CORS khi gọi API từ Frontend ReactJS?','Xin chào! Tôi rất vui được giúp bạn giải quyết vấn đề CORS trong ứng dụng ReactJS của mình.\n\n**Làm thế nào để sửa lỗi CORS khi gọi API từ Frontend ReactJS?**\n\nCORS (Cross-Origin Resource Sharing) là một tính năng bảo mật của trình duyệt web, giúp ngăn chặn mã độc từ trang web khác truy cập vào dữ liệu của trang web hiện tại. Tuy nhiên, khi gọi API từ frontend ReactJS, bạn có thể gặp phải lỗi CORS nếu API được phục vụ trên một miền khác với frontend của bạn.\n\nDưới đây là một số cách để sửa lỗi CORS khi gọi API từ frontend ReactJS:\n\n### 1. Cài đặt CORS trên server API\n\nNếu bạn có quyền truy cập vào server API, bạn có thể cài đặt CORS bằng cách thêm header `Access-Control-Allow-Origin` vào phản hồi HTTP. Ví dụ:\n\n```http\nAccess-Control-Allow-Origin: *\n```\n\nHoặc nếu bạn muốn chỉ cho phép frontend của mình truy cập:\n\n```http\nAccess-Control-Allow-Origin: http://localhost:3000\n```\n\n### 2. Sử dụng proxy server\n\nBạn có thể sử dụng proxy server để chuyển yêu cầu API đến server API gốc. Ví dụ, bạn có thể sử dụng `http-proxy-middleware` trong ReactJS:\n\n```javascript\nconst express = require(\'express\');\nconst proxy = require(\'http-proxy-middleware\');\n\nconst app = express();\n\napp.use(\n  \'/api\',\n  proxy({\n    target: \'http://api.example.com\',\n    changeOrigin: true,\n  })\n);\n\napp.listen(3000, () => {\n  console.log(\'Server đã sẵn sàng\');\n});\n```\n\n### 3. Sử dụng library CORS trong ReactJS\n\nBạn có thể sử dụng library `cors` trong ReactJS để thêm header CORS vào phản hồi HTTP:\n\n```javascript\nimport cors from \'cors\';\n\nconst app = express();\n\napp.use(cors());\n\napp.listen(3000, () => {\n  console.log(\'Server đã sẵn sàng\');\n});\n```\n\n### 4. Sử dụng option Preflight trong ReactJS\n\nBạn có thể sử dụng option Preflight để thêm header CORS vào phản hồi HTTP:\n\n```javascript\nfetch(\'http://api.example.com/data\', {\n  method: \'POST\',\n  headers: {\n    \'Content-Type\': \'application/json\',\n  },\n  mode: \'cors\',\n})\n  .then((response) => response.json())\n  .then((data) => console.log(data))\n  .catch((error) => console.error(error));\n```\n\nHy vọng những cách trên sẽ giúp bạn sửa lỗi CORS khi gọi API từ frontend ReactJS của mình!',0,'2026-04-23 18:00:08'),(7,8,'đây là gì','Chào bạn học sinh!\n\nHình ảnh bạn gửi cho thấy một **con hẻm hoặc khu phố nhỏ rất đặc trưng ở Nhật Bản vào buổi tối**.\n\nHãy cùng \"khám phá\" bức ảnh này nhé:\n\n1.  **Đèn lồng đỏ (Chōchin):** Đây là điểm nhấn nổi bật nhất! Những chiếc đèn lồng đỏ truyền thống của Nhật Bản (gọi là Chōchin) được treo dọc con hẻm, tạo ra một ánh sáng ấm áp, mời gọi và mang đậm nét văn hóa. Chúng không chỉ dùng để chiếu sáng mà còn là biểu tượng của các quán ăn, quán bar (izakaya) hoặc các cửa hàng.\n2.  **Không gian hẹp và ấm cúng:** Con hẻm khá hẹp, với hai bên là những dãy nhà san sát nhau. Kiểu kiến trúc này rất phổ biến ở các thành phố lớn của Nhật Bản, nơi các không gian nhỏ được tận dụng tối đa để tạo ra những khu phố thương mại sầm uất và độc đáo.\n3.  **Các biển hiệu và cửa hàng:** Bạn có thể thấy nhiều biển hiệu với chữ tiếng Nhật, cho thấy đây là nơi tập trung nhiều quán ăn, quán rượu nhỏ (izakaya), quán bar hoặc các cửa hàng đặc sản. Điều này tạo nên một không khí rất sôi động và chân thực về đời sống đô thị Nhật Bản.\n4.  **Bầu không khí đêm:** Ánh đèn từ các cửa hàng và đèn lồng chiếu sáng cả con hẻm, tạo nên một vẻ đẹp lung linh, huyền ảo và đầy chất thơ vào ban đêm. Có vẻ như trời vừa mưa hoặc sàn nhà ẩm ướt, phản chiếu ánh sáng càng làm tăng thêm vẻ đẹp của khung cảnh.\n\nTóm lại, đây là một hình ảnh sống động, thể hiện nét đẹp vừa truyền thống vừa hiện đại của đô thị Nhật Bản, đặc biệt là vào ban đêm, nơi những con hẻm nhỏ lại trở thành trung tâm của ẩm thực và giải trí!\n\nThật thú vị phải không nào? Hy vọng giải đáp này giúp bạn hiểu rõ hơn về bức ảnh nhé!',0,'2026-04-23 18:29:34'),(8,8,'Tạo cho mình một bài tập Python cơ bản để luyện vòng lặp For.','Chào bạn, một câu hỏi rất hay và là bước khởi đầu vững chắc để làm quen với Python đó! Vòng lặp `for` là một trong những công cụ cơ bản và mạnh mẽ nhất của lập trình, giúp chúng ta thực hiện các tác vụ lặp đi lặp lại một cách hiệu quả.\n\nHãy cùng làm một bài tập nho nhỏ để luyện tập vòng lặp `for` nhé!\n\n---\n\n### **Bài Tập Python Cơ Bản: Bảng Cửu Chương Đơn Giản**\n\n**Mục tiêu:** Sử dụng vòng lặp `for` để tạo ra bảng cửu chương cho một số bất kỳ từ 1 đến 10.\n\n**Yêu cầu:**\n\n1.  Cho một số nguyên (ví dụ: `so_can_tao_bang = 7`).\n2.  Sử dụng vòng lặp `for` để tính và in ra kết quả của số đó nhân với các số từ 1 đến 10.\n3.  Định dạng kết quả sao cho dễ đọc (ví dụ: `7 x 1 = 7`).\n\n---\n\n#### **Gợi ý nhỏ trước khi bạn bắt đầu (nếu cần):**\n\n*   Bạn có thể dùng hàm `range()` để tạo ra một chuỗi số. Nhớ rằng `range(start, end)` sẽ tạo ra các số từ `start` đến `end-1`.\n*   Sử dụng f-string (chuỗi định dạng) để in ra kết quả đẹp mắt. Ví dụ: `f\"{bien1} x {bien2} = {ket_qua}\"`\n\n---\n\n#### **Ví dụ về đầu ra mong muốn (nếu số là 7):**\n\n```\nBảng cửu chương của 7:\n7 x 1 = 7\n7 x 2 = 14\n7 x 3 = 21\n7 x 4 = 28\n7 x 5 = 35\n7 x 6 = 42\n7 x 7 = 49\n7 x 8 = 56\n7 x 9 = 63\n7 x 10 = 70\n```\n\n---\n\n#### **Giải pháp (sau khi bạn đã thử sức nhé!):**\n\n```python\nprint(\"--- Bài Tập Luyện Vòng Lặp For: Bảng Cửu Chương ---\")\n\n# 1. Định nghĩa số bạn muốn tạo bảng cửu chương\nso_can_tao_bang = 7\n\nprint(f\"\\nBảng cửu chương của {so_can_tao_bang}:\")\n\n# 2. Sử dụng vòng lặp for\n#    \'i\' sẽ chạy từ 1 đến 10 (range(1, 11) nghĩa là 1, 2, ..., 10)\nfor i in range(1, 11):\n    # 3. Tính toán kết quả\n    ket_qua = so_can_tao_bang * i\n\n    # 4. In kết quả theo định dạng mong muốn\n    print(f\"{so_can_tao_bang} x {i} = {ket_qua}\")\n\nprint(\"\\n----------------------------------------------------\")\n```\n\n---\n\n#### **Giải thích từng phần:**\n\n1.  **`so_can_tao_bang = 7`**: Chúng ta khởi tạo một biến `so_can_tao_bang` và gán giá trị `7` cho nó. Đây là số mà bạn muốn tạo bảng cửu chương. Bạn có thể thay đổi số này để thử với các bảng cửu chương khác!\n2.  **`for i in range(1, 11):`**:\n    *   Đây chính là vòng lặp `for` của chúng ta!\n    *   `range(1, 11)` tạo ra một chuỗi các số nguyên từ `1` đến `10` (nhớ rằng số cuối cùng trong `range` luôn bị loại trừ, nên để có 10, ta phải dùng 11).\n    *   Mỗi lần lặp, biến `i` sẽ nhận một giá trị mới từ chuỗi này (lần lượt là 1, 2, 3, ..., 10).\n3.  **`ket_qua = so_can_tao_bang * i`**: Bên trong mỗi vòng lặp, chúng ta tính toán kết quả bằng cách nhân `so_can_tao_bang` với giá trị hiện tại của `i`.\n4.  **`print(f\"{so_can_tao_bang} x {i} = {ket_qua}\")`**:\n    *   Sử dụng một f-string (chuỗi định dạng) để tạo ra một dòng văn bản dễ đọc.\n    *   `{so_can_tao_bang}`, `{i}`, và `{ket_qua}` sẽ được thay thế bằng các giá trị thực của các biến đó trong mỗi lần lặp.\n\n---\n\n### **Bài tập mở rộng (nâng cao hơn một chút):**\n\nHãy thử sửa đổi chương trình trên để:\n\n*   Hỏi người dùng nhập vào số họ muốn tạo bảng cửu chương (sử dụng hàm `input()`).\n*   In ra bảng cửu chương đó.\n\nBạn sẽ làm được thôi! Đừng ngại thử nghiệm, thay đổi các con số, và xem điều gì sẽ xảy ra nhé. Đó là cách tốt nhất để học lập trình đấy.\n\nNếu có bất kỳ câu hỏi nào khác, đừng ngần ngại hỏi mình nha! Chúc bạn học tốt với AI Study Hub LMS!',0,'2026-04-23 18:29:56'),(9,10,'Tạo cho mình một bài tập Python cơ bản để luyện vòng lặp For.','Xin chào! Tôi rất vui được giúp bạn tạo một bài tập Python cơ bản để luyện vòng lặp For.\n\n**Bài tập:**\n\n### Bài tập 1: In ra các số từ 1 đến 10\n\nViết một chương trình Python để in ra các số từ 1 đến 10 sử dụng vòng lặp For.\n\n#### Mô tả:\n\n* Sử dụng vòng lặp For để lặp qua các số từ 1 đến 10.\n* In ra mỗi số trong khoảng số này.\n\n#### Gợi ý:\n\n* Sử dụng biểu thức `range(1, 11)` để tạo một phạm vi số từ 1 đến 10.\n* Sử dụng vòng lặp For để lặp qua các số trong phạm vi này.\n\n#### Mã nguồn:\n\n```python\n# Tạo một phạm vi số từ 1 đến 10\nfor i in range(1, 11):\n    print(i)\n```\n\n#### Kết quả:\n\nKhi chạy chương trình này, bạn sẽ thấy các số từ 1 đến 10 được in ra.\n\n### Bài tập 2: In ra các chữ cái trong một chuỗi\n\nViết một chương trình Python để in ra các chữ cái trong một chuỗi sử dụng vòng lặp For.\n\n#### Mô tả:\n\n* Sử dụng vòng lặp For để lặp qua các chữ cái trong một chuỗi.\n* In ra mỗi chữ cái trong chuỗi.\n\n#### Gợi ý:\n\n* Sử dụng biểu thức `chuỗi` để tạo một chuỗi ký tự.\n* Sử dụng vòng lặp For để lặp qua các chữ cái trong chuỗi.\n\n#### Mã nguồn:\n\n```python\nchuỗi = \"hello\"\nfor i in chuỗi:\n    print(i)\n```\n\n#### Kết quả:\n\nKhi chạy chương trình này, bạn sẽ thấy các chữ cái trong chuỗi \"hello\" được in ra.\n\n### Bài tập 3: In ra các số chẵn từ 1 đến 20\n\nViết một chương trình Python để in ra các số chẵn từ 1 đến 20 sử dụng vòng lặp For.\n\n#### Mô tả:\n\n* Sử dụng vòng lặp For để lặp qua các số từ 1 đến 20.\n* In ra mỗi số chẵn trong khoảng số này.\n\n#### Gợi ý:\n\n* Sử dụng biểu thức `range(1, 21)` để tạo một phạm vi số từ 1 đến 20.\n* Sử dụng vòng lặp For để lặp qua các số trong phạm vi này và kiểm tra xem số đó có chẵn hay không.\n\n#### Mã nguồn:\n\n```python\nfor i in range(1, 21):\n    if i % 2 == 0:\n        print(i)\n```\n\n#### Kết quả:\n\nKhi chạy chương trình này, bạn sẽ thấy các số chẵn từ 1 đến 20 được in ra.\n\nHy vọng các bài tập này sẽ giúp bạn luyện vòng lặp For trong Python!',0,'2026-04-24 14:37:27'),(10,10,'Giải thích cho mình khái niệm Lập trình Hướng đối tượng (OOP) theo cách dễ hiểu nhất.','Chào bạn! Tôi rất vui được giúp bạn hiểu về khái niệm Lập trình Hướng đối tượng (OOP).\n\n**Lập trình Hướng đối tượng (OOP) là gì?**\n\nLập trình Hướng đối tượng (OOP) là một cách thức lập trình mà bạn sử dụng các đối tượng (object) để xây dựng chương trình. Một đối tượng là một thực thể có các đặc điểm và hành vi riêng biệt.\n\n**Làm thế nào OOP hoạt động?**\n\nKhi bạn sử dụng OOP, bạn sẽ tạo ra các đối tượng có thể tương tác với nhau. Mỗi đối tượng có:\n\n1. **Tính chất (Attribute)**: Những đặc điểm của đối tượng, như màu sắc, kích thước,...\n2. **Hành vi (Method)**: Những gì mà đối tượng có thể làm, như di chuyển, vẽ,...\n3. **Quan hệ (Relationship)**: Những cách mà các đối tượng tương tác với nhau.\n\n**Ví dụ để hiểu rõ hơn**\n\nHãy tưởng tượng bạn đang tạo một chương trình mô phỏng một trường học. Bạn có thể tạo các đối tượng như:\n\n* **Trường học** (School): có tính chất như số lượng học sinh, số lượng giáo viên,... và hành vi như tạo lớp học, thêm học sinh,...\n* **Học sinh** (Student): có tính chất như tên, tuổi,... và hành vi như đi học, học bài học,...\n* **Giáo viên** (Teacher): có tính chất như tên, tuổi,... và hành vi như giảng bài, kiểm tra học sinh,...\n\nKhi bạn sử dụng OOP, bạn có thể tạo các mối quan hệ giữa các đối tượng, như:\n\n* Một học sinh có thể học trong một lớp học, do một giáo viên giảng dạy.\n* Một giáo viên có thể dạy cho nhiều học sinh.\n\n**Lợi ích của OOP**\n\nOOP mang lại nhiều lợi ích, bao gồm:\n\n* **Tính tái sử dụng**: Bạn có thể sử dụng các đối tượng đã tạo ra để xây dựng các chương trình khác.\n* **Tính linh hoạt**: Bạn có thể dễ dàng thay đổi hoặc cập nhật các đối tượng mà không ảnh hưởng đến chương trình.\n* **Tính dễ hiểu**: OOP giúp bạn xây dựng chương trình một cách logic và dễ hiểu.\n\nTóm lại, Lập trình Hướng đối tượng (OOP) là một cách thức lập trình sử dụng các đối tượng để xây dựng chương trình. Mỗi đối tượng có tính chất, hành vi và quan hệ với các đối tượng khác. OOP mang lại nhiều lợi ích, bao gồm tính tái sử dụng, tính linh hoạt và tính dễ hiểu.',0,'2026-04-24 14:37:31'),(11,10,'chào bạn','Xin chào! Tôi rất vui được gặp bạn trên AI Study Hub LMS. Tôi ở đây để giúp đỡ và hỗ trợ bạn trong quá trình học tập. Bạn cần giúp đỡ gì hôm nay?',0,'2026-04-24 14:37:40'),(12,10,'Tạo cho mình một bài tập Python cơ bản để luyện vòng lặp For.','Chào bạn!\n\nTôi có thể giúp bạn tạo một bài tập Python cơ bản để luyện vòng lặp For. Dưới đây là một ví dụ:\n\n**Bài tập:** In ra tên các màu sắc trong một danh sách.\n\n**Mục tiêu:** Viết một chương trình Python sử dụng vòng lặp For để in ra tên các màu sắc trong một danh sách.\n\n**Dữ liệu:**\n\n- Danh sách màu sắc: `màu_sắc = [\"Đỏ\", \"Xanh\", \"Vàng\", \"Trắng\", \"Đen\"]`\n\n**Yêu cầu:**\n\n- Sử dụng vòng lặp For để lặp qua danh sách màu sắc.\n- In ra tên mỗi màu sắc.\n\n**Code:**\n```python\n# Danh sách màu sắc\nmàu_sắc = [\"Đỏ\", \"Xanh\", \"Vàng\", \"Trắng\", \"Đen\"]\n\n# Sử dụng vòng lặp For để lặp qua danh sách màu sắc\nfor màu in màu_sắc:\n    # In ra tên mỗi màu sắc\n    print(màu)\n```\n**Kết quả:**\nKhi chạy chương trình trên, bạn sẽ thấy tên các màu sắc được in ra như sau:\n```\nĐỏ\nXanh\nVàng\nTrắng\nĐen\n```\n**Lời giải:**\n\n- Chúng ta đã tạo một danh sách màu sắc `màu_sắc` chứa các tên màu.\n- Chúng ta sử dụng vòng lặp For để lặp qua danh sách màu sắc.\n- Trong vòng lặp, chúng ta sử dụng `màu` để đại diện cho mỗi màu sắc trong danh sách.\n- Chúng ta sử dụng `print(màu)` để in ra tên mỗi màu sắc.\n\n**Bài tập này giúp bạn:**\n\n- Tìm hiểu cách sử dụng vòng lặp For trong Python.\n- Luyện tập cách in ra dữ liệu từ một danh sách.\n- Áp dụng kiến thức này vào các tình huống khác trong lập trình Python.\n\nHy vọng bài tập này hữu ích cho bạn!',0,'2026-04-24 14:39:40'),(13,11,'chào','Xin chào! Tôi rất vui được giúp đỡ bạn trên nền tảng AI Study Hub LMS. Bạn cần giúp đỡ về gì hôm nay?',0,'2026-04-25 03:06:55'),(14,12,'Giải thích cho mình khái niệm Lập trình Hướng đối tượng (OOP) theo cách dễ hiểu nhất.','Chào bạn! Tôi rất hân hạnh được giúp bạn hiểu rõ hơn về Lập trình Hướng đối tượng (OOP).\n\n**Lập trình Hướng đối tượng (OOP) là gì?**\n\nLập trình Hướng đối tượng (OOP) là một phương pháp lập trình giúp bạn tạo ra các chương trình phức tạp bằng cách sử dụng các đơn vị nhỏ, gọi là \"đối tượng\" (Object). Mỗi đối tượng có các đặc tính và hành vi riêng, giúp bạn mô tả và tương tác với thế giới thực tế.\n\n**Các đặc tính của OOP:**\n\n1. **Đối tượng (Object)**: Một đơn vị nhỏ có các đặc tính và hành vi riêng. Ví dụ như một người, một xe ô tô, một sản phẩm...\n2. **Class**: Một mẫu hoặc bản thiết kế cho một loại đối tượng. Ví dụ như \"Người\", \"Xe ô tô\", \"Sản phẩm...\"\n3. **Thuộc tính (Attribute)**: Các đặc tính của một đối tượng, như tên, tuổi, màu sắc...\n4. **Phương thức (Method)**: Các hành vi của một đối tượng, như di chuyển, nói...\n5. **Kế thừa (Inheritance)**: Một đối tượng có thể kế thừa các đặc tính và hành vi từ một đối tượng khác.\n6. **Trình bày (Polymorphism)**: Một đối tượng có thể biểu diễn nhiều hình dạng khác nhau.\n\n**Ví dụ:**\n\nHãy tưởng tượng bạn đang tạo ra một chương trình để mô tả một người. Bạn có thể tạo ra một đối tượng \"Người\" và thêm vào các thuộc tính như:\n\n* Tên\n* Tuổi\n* Màu tóc\n\nVà các phương thức như:\n\n* Nói\n* Đi\n* Ăn\n\nKhi bạn tạo ra một người cụ thể, như \"Nguyễn Văn A\", bạn có thể thêm vào các thuộc tính và phương thức cụ thể cho người đó.\n\n**Lợi ích của OOP:**\n\nLập trình Hướng đối tượng giúp bạn:\n\n* Tạo ra các chương trình phức tạp dễ dàng\n* Mô tả và tương tác với thế giới thực tế\n* Sử dụng lại mã nguồn\n* Cải thiện độ tin cậy và hiệu suất\n\nHy vọng thông tin này giúp bạn hiểu rõ hơn về Lập trình Hướng đối tượng! Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại hỏi tôi.',0,'2026-04-25 04:11:01'),(15,12,'chào','Xin chào! Tôi rất hân hạnh được giúp đỡ bạn ở AI Study Hub LMS. Bạn cần hỗ trợ về gì hôm nay?',0,'2026-04-25 04:11:08'),(16,12,'chào','Xin chào! Tôi rất vui được giúp đỡ bạn trên nền tảng AI Study Hub LMS. Bạn cần giúp đỡ về một chủ đề cụ thể hay chỉ muốn trò chuyện một chút? Hãy cho tôi biết và tôi sẽ cố gắng hỗ trợ bạn một cách tốt nhất có thể!',0,'2026-04-25 04:12:21'),(17,8,'tóm tắt bài học những điểm cần biết','Xin chào! Tôi xin tóm tắt lại những điểm cần biết về bài học vừa qua.\n\n**Bài học:**\n\n*   **Khái niệm về lập trình và ngôn ngữ lập trình:** Lập trình là quá trình viết các chương trình máy tính để thực hiện các tác vụ nhất định. Ngôn ngữ lập trình là một hệ thống ngôn ngữ được thiết kế để viết các chương trình máy tính.\n*   **Khái niệm về biến và kiểu dữ liệu:** Biến là một tên được gán cho một giá trị trong chương trình. Kiểu dữ liệu là loại dữ liệu mà biến có thể chứa.\n*   **Khái niệm về toán tử:** Toán tử là các ký hiệu được sử dụng để biểu diễn các phép toán trong chương trình.\n*   **Khái niệm về cấu trúc điều kiện (if, switch):** Cấu trúc điều kiện là các câu lệnh được sử dụng để thực hiện các tác vụ khác nhau dựa trên các điều kiện nhất định.\n*   **Khái niệm về vòng lặp (for, while):** Vòng lặp là các câu lệnh được sử dụng để thực hiện các tác vụ lặp đi lặp lại một cách có kiểm soát.\n*   **Khái niệm về hàm (function):** Hàm là một tập hợp các câu lệnh được viết một cách độc lập và có thể được gọi lại nhiều lần trong chương trình.\n*   **Khái niệm về mảng và chuỗi:** Mảng và chuỗi là các cấu trúc dữ liệu được sử dụng để lưu trữ nhiều giá trị trong một biến.\n*   **Khái niệm về thuật toán:** Thuật toán là một tập hợp các bước được thực hiện để giải quyết một vấn đề nhất định.\n*   **Khái niệm về lập trình cơ bản và tư duy giải quyết vấn đề:** Lập trình cơ bản là việc viết các chương trình máy tính để thực hiện các tác vụ nhất định. Tư duy giải quyết vấn đề là việc sử dụng các công cụ và kỹ thuật để giải quyết các vấn đề phức tạp.\n\n**Bài tập:**\n\n*   **Bài tập 1:** Viết một chương trình Python để tạo ra bảng cửu chương cho một số bất kỳ từ 1 đến 10.\n*   **Bài tập 2:** Viết một chương trình Python để hỏi người dùng nhập vào số họ muốn tạo bảng cửu chương và in ra bảng cửu chương đó.\n\nHy vọng những điểm cần biết trên sẽ giúp bạn hiểu rõ hơn về lập trình và các khái niệm cơ bản trong lập trình. Nếu bạn có bất kỳ câu hỏi nào khác, đừng ngần ngại hỏi mình nha! Chúc bạn học tốt với AI Study Hub LMS!',0,'2026-05-07 07:59:12'),(18,8,'giúp tôi viết một đoạn code cho bài tập bạn đã đưa ra','Tôi xin giúp bạn viết một đoạn code cho bài tập về bảng cửu chương.\n\n**Bài Tập:**\n\n*   **Viết một chương trình Python để tạo ra bảng cửu chương cho một số bất kỳ từ 1 đến 10.**\n*   **Viết một chương trình Python để hỏi người dùng nhập vào số họ muốn tạo bảng cửu chương và in ra bảng cửu chương đó.**\n\n**Đoạn Code:**\n\n```python\n# Bài Tập 1: Tạo ra bảng cửu chương cho một số bất kỳ từ 1 đến 10\n\ndef tao_bang_cuu_chuong(so):\n    print(f\"\\nBảng cửu chương của {so}:\")\n    for i in range(1, 11):\n        ket_qua = so * i\n        print(f\"{so} x {i} = {ket_qua}\")\n\n# Bài Tập 2: Hỏi người dùng nhập vào số họ muốn tạo bảng cửu chương\n\ndef hoi_nguoi_dungNhap_so():\n    so = int(input(\"Xin mời nhập số bạn muốn tạo bảng cửu chương: \"))\n    tao_bang_cuu_chuong(so)\n\n# Chạy chương trình\ntao_bang_cuu_chuong(7)  # Ví dụ: tạo bảng cửu chương của số 7\nhoi_nguoi_dungNhap_so()  # Hỏi người dùng nhập vào số họ muốn tạo bảng cửu chương\n```\n\n**Giải thích Đoạn Code:**\n\n1.  **`def tao_bang_cuu_chuong(so):`**: Định nghĩa một hàm `tao_bang_cuu_chuong` với tham số `so` (số bạn muốn tạo bảng cửu chương).\n2.  **`print(f\"\\nBảng cửu chương của {so}:\")`**: In ra tiêu đề bảng cửu chương của số `so`.\n3.  **`for i in range(1, 11):`**: Sử dụng vòng lặp `for` để lặp qua các số từ 1 đến 10.\n4.  **`ket_qua = so * i`**: Tính toán kết quả của số `so` nhân với số `i` hiện tại.\n5.  **`print(f\"{so} x {i} = {ket_qua}\")`**: In ra kết quả tính toán ở bước trên dưới dạng \"số x i = kết quả\".\n6.  **`def hoi_nguoi_dungNhap_so():`**: Định nghĩa một hàm `hoi_nguoi_dungNhap_so` để hỏi người dùng nhập vào số họ muốn tạo bảng cửu chương.\n7.  **`so = int(input(\"Xin mời nhập số bạn muốn tạo bảng cửu chương: \"))`**: Hỏi người dùng nhập vào số và lưu trữ nó vào biến `so`.\n8.  **`tao_bang_cuu_chuong(so)`**: Gọi hàm `tao_bang_cuu_chuong` với số người dùng vừa nhập để tạo bảng cửu chương.\n\nHy vọng đoạn code trên sẽ giúp bạn hiểu rõ hơn về cách tạo ra bảng cửu chương bằng Python. Nếu bạn có bất kỳ câu hỏi nào khác, đừng ngần ngại hỏi mình nha! Chúc bạn học tốt với AI Study Hub LMS!',0,'2026-05-07 08:00:13'),(19,8,'tóm tắt bài học những điểm cần biết','Xin chào! Tôi xin tóm tắt lại những điểm cần biết về bài học vừa qua.\n\n**Bài học:**\n\n*   **Khái niệm về lập trình và ngôn ngữ lập trình:** Lập trình là quá trình viết các chương trình máy tính để thực hiện các tác vụ nhất định. Ngôn ngữ lập trình là một hệ thống ngôn ngữ được thiết kế để viết các chương trình máy tính.\n*   **Khái niệm về biến và kiểu dữ liệu:** Biến là một tên được gán cho một giá trị trong chương trình. Kiểu dữ liệu là loại dữ liệu mà biến có thể chứa.\n*   **Khái niệm về toán tử:** Toán tử là các ký hiệu được sử dụng để biểu diễn các phép toán trong chương trình.\n*   **Khái niệm về cấu trúc điều kiện (if, switch):** Cấu trúc điều kiện là các câu lệnh được sử dụng để thực hiện các tác vụ khác nhau dựa trên các điều kiện nhất định.\n*   **Khái niệm về vòng lặp (for, while):** Vòng lặp là các câu lệnh được sử dụng để thực hiện các tác vụ lặp đi lặp lại một cách có kiểm soát.\n*   **Khái niệm về hàm (function):** Hàm là một tập hợp các câu lệnh được viết một cách độc lập và có thể được gọi lại nhiều lần trong chương trình.\n*   **Khái niệm về mảng và chuỗi:** Mảng và chuỗi là các cấu trúc dữ liệu được sử dụng để lưu trữ nhiều giá trị trong một biến.\n*   **Khái niệm về thuật toán:** Thuật toán là một tập hợp các bước được thực hiện để giải quyết một vấn đề nhất định.\n*   **Khái niệm về lập trình cơ bản và tư duy giải quyết vấn đề:** Lập trình cơ bản là việc viết các chương trình máy tính để thực hiện các tác vụ nhất định. Tư duy giải quyết vấn đề là việc sử dụng các công cụ và kỹ thuật để giải quyết các vấn đề phức tạp.\n\n**Bài tập:**\n\n*   **Bài tập 1:** Viết một chương trình Python để tạo ra bảng cửu chương cho một số bất kỳ từ 1 đến 10.\n*   **Bài tập 2:** Viết một chương trình Python để hỏi người dùng nhập vào số họ muốn tạo bảng cửu chương và in ra bảng cửu chương đó.\n\n**Đoạn Code:**\n\n```python\n# Bài Tập 1: Tạo ra bảng cửu chương cho một số bất kỳ từ 1 đến 10\n\ndef tao_bang_cuu_chuong(so):\n    print(f\"\\nBảng cửu chương của {so}:\")\n    for i in range(1, 11):\n        ket_qua = so * i\n        print(f\"{so} x {i} = {ket_qua}\")\n\n# Bài Tập 2: Hỏi người dùng nhập vào số họ muốn tạo bảng cửu chương\n\ndef hoi_nguoi_dungNhap_so():\n    so = int(input(\"Xin mời nhập số bạn muốn tạo bảng cửu chương: \"))\n    tao_bang_cuu_chuong(so)\n\n# Chạy chương trình\ntao_bang_cuu_chuong(7)  # Ví dụ: tạo bảng cửu chương của số 7\nhoi_nguoi_dungNhap_so()  # Hỏi người dùng nhập vào số họ muốn tạo bảng cửu chương\n```\n\nHy vọng những điểm cần biết trên sẽ giúp bạn hiểu rõ hơn về lập trình và các khái niệm cơ bản trong lập trình. Nếu bạn có bất kỳ câu hỏi nào khác, đừng ngần ngại hỏi mình nha! Chúc bạn học tốt với AI Study Hub LMS!',0,'2026-05-07 08:03:05'),(20,8,'bạn thấy khóa học như nào','Tôi thấy khóa học \"Nhập Môn Lập Trình\" là một khóa học cơ bản và nền tảng về lập trình. Khóa học này được thiết kế để giúp học viên mới bắt đầu với lập trình và hiểu được các khái niệm cơ bản.\n\n**Điểm mạnh của khóa học:**\n\n*   **Cơ bản và nền tảng:** Khóa học này giúp học viên hiểu được các khái niệm cơ bản về lập trình, bao gồm ngôn ngữ lập trình, biến, kiểu dữ liệu, toán tử, cấu trúc điều kiện, vòng lặp, hàm, mảng và chuỗi.\n*   **Thuật toán và tư duy giải quyết vấn đề:** Khóa học này giúp học viên hiểu được các công cụ và kỹ thuật để giải quyết các vấn đề phức tạp.\n*   **Phát triển kỹ năng lập trình cơ bản:** Khóa học này giúp học viên phát triển kỹ năng lập trình cơ bản và có thể viết các chương trình máy tính để thực hiện các tác vụ nhất định.\n\n**Điểm cần cải thiện:**\n\n*   **Thực hành và bài tập nhiều hơn:** Khóa học này cần có nhiều bài tập và thực hành hơn để giúp học viên áp dụng các khái niệm cơ bản vào thực tế.\n*   **Thể hiện các ví dụ thực tế hơn:** Khóa học này cần có các ví dụ thực tế hơn để giúp học viên hiểu được các khái niệm cơ bản và áp dụng vào thực tế.\n*   **Cải thiện giao diện và trải nghiệm người dùng:** Khóa học này cần có giao diện và trải nghiệm người dùng tốt hơn để giúp học viên học tập hiệu quả hơn.\n\n**Tổng kết:**\n\n*   **Khóa học này là một lựa chọn tốt cho những người mới bắt đầu với lập trình:** Khóa học này giúp học viên hiểu được các khái niệm cơ bản về lập trình và phát triển kỹ năng lập trình cơ bản.\n*   **Khóa học này cần có nhiều bài tập và thực hành hơn:** Khóa học này cần có nhiều bài tập và thực hành hơn để giúp học viên áp dụng các khái niệm cơ bản vào thực tế.',0,'2026-05-07 08:05:36'),(21,8,'giúp tôi tóm tắt nội dung chính','Dưới đây là tóm tắt nội dung chính của khóa học \"Nhập Môn Lập Trình\":\n\n*   **Khái niệm về lập trình:** Lập trình là quá trình viết các chương trình máy tính để thực hiện các tác vụ nhất định.\n*   **Ngôn ngữ lập trình:** Ngôn ngữ lập trình là một hệ thống ngôn ngữ được thiết kế để viết các chương trình máy tính.\n*   **Biến và kiểu dữ liệu:** Biến là một tên được gán cho một giá trị trong chương trình. Kiểu dữ liệu là loại dữ liệu mà biến có thể chứa.\n*   **Toán tử:** Toán tử là các ký hiệu được sử dụng để biểu diễn các phép toán trong chương trình.\n*   **Cấu trúc điều kiện và vòng lặp:** Cấu trúc điều kiện và vòng lặp là các câu lệnh được sử dụng để thực hiện các tác vụ khác nhau dựa trên các điều kiện nhất định và thực hiện các tác vụ lặp đi lặp lại một cách có kiểm soát.\n*   **Hàm và mảng chuỗi:** Hàm và mảng chuỗi là các cấu trúc dữ liệu được sử dụng để lưu trữ nhiều giá trị trong một biến.\n*   **Thuật toán và tư duy giải quyết vấn đề:** Thuật toán và tư duy giải quyết vấn đề là các công cụ và kỹ thuật được sử dụng để giải quyết các vấn đề phức tạp.\n\n**Mục tiêu khóa học:**\n\n*   **Học viên hiểu được các khái niệm cơ bản về lập trình:** Khóa học này giúp học viên hiểu được các khái niệm cơ bản về lập trình, bao gồm ngôn ngữ lập trình, biến, kiểu dữ liệu, toán tử, cấu trúc điều kiện, vòng lặp, hàm, mảng và chuỗi.\n*   **Học viên phát triển kỹ năng lập trình cơ bản:** Khóa học này giúp học viên phát triển kỹ năng lập trình cơ bản và có thể viết các chương trình máy tính để thực hiện các tác vụ nhất định.\n*   **Học viên áp dụng các khái niệm cơ bản vào thực tế:** Khóa học này giúp học viên áp dụng các khái niệm cơ bản vào thực tế và giải quyết các vấn đề phức tạp.',0,'2026-05-07 08:06:40'),(22,6,'tóm tắt bài học những điểm cần biết','Dưới đây là tóm tắt các điểm cần biết của bài học Giới thiệu về AI:\n\n**Khái niệm AI:**\n\n- Trí Tuệ Nhân Tạo (AI) là một lĩnh vực nghiên cứu và phát triển công nghệ giúp máy tính và máy móc có thể thực hiện các nhiệm vụ thông minh như con người.\n- AI mô phỏng tư duy con người thông qua học dữ liệu và phân tích thông tin.\n\n**Các loại AI phổ biến:**\n\n- AI phản hồi tự động: giúp máy tính trả lời các câu hỏi và thực hiện các nhiệm vụ dựa trên dữ liệu được huấn luyện.\n- Machine Learning: giúp máy tính học hỏi từ dữ liệu và cải thiện khả năng dự đoán và phân tích.\n- Deep Learning: một loại Machine Learning sử dụng các mô hình thần kinh để học hỏi và phân tích dữ liệu phức tạp.\n\n**Cách AI hoạt động:**\n\n- Bước 1: Thu thập dữ liệu\n- Bước 2: Huấn luyện mô hình\n- Bước 3: Dự đoán kết quả\n\n**Ưu điểm và hạn chế của AI:**\n\n- Ưu điểm: giúp tăng cường hiệu suất công việc, cải thiện chất lượng sản phẩm và dịch vụ.\n- Hạn chế: có thể gây ra rủi ro về việc mất việc làm, mất dữ liệu và mất kiểm soát.\n\n**Ứng dụng thực tế của AI:**\n\n- Giáo dục: giúp tạo ra các tài liệu học tập và thực hiện các nhiệm vụ giáo dục tự động.\n- Học tập: giúp tăng cường khả năng học hỏi và cải thiện kết quả học tập.\n- Đời sống: giúp tăng cường hiệu suất công việc, cải thiện chất lượng sản phẩm và dịch vụ.',0,'2026-05-08 05:16:37'),(23,6,'tóm tắt bài học những điểm cần biết','Dưới đây là tóm tắt các điểm cần biết của bài học Giới thiệu về AI:\n\n**Khái niệm và mục tiêu:**\n\n- Trí Tuệ Nhân Tạo (AI) giúp máy tính và máy móc có thể thực hiện các nhiệm vụ thông minh như con người.\n- Mục tiêu của AI là mô phỏng tư duy con người thông qua học dữ liệu và phân tích thông tin.\n\n**Các loại AI phổ biến:**\n\n- AI phản hồi tự động\n- Machine Learning\n- Deep Learning\n\n**Cách AI hoạt động:**\n\n- Bước 1: Thu thập dữ liệu\n- Bước 2: Huấn luyện mô hình\n- Bước 3: Dự đoán kết quả\n\n**Ưu điểm và hạn chế của AI:**\n\n- Ưu điểm: tăng cường hiệu suất công việc, cải thiện chất lượng sản phẩm và dịch vụ.\n- Hạn chế: rủi ro về việc mất việc làm, mất dữ liệu và mất kiểm soát.\n\n**Ứng dụng thực tế của AI:**\n\n- Giáo dục\n- Học tập\n- Đời sống',0,'2026-05-08 05:18:44'),(24,6,'tóm tắt bài học những điểm cần biết','Dưới đây là tóm tắt các điểm cần biết của bài học Giới thiệu về AI:\n\n* **Khái niệm AI:** Trí Tuệ Nhân Tạo (AI) giúp máy tính và máy móc thực hiện các nhiệm vụ thông minh như con người.\n* **Các loại AI:** AI phản hồi tự động, Machine Learning, Deep Learning.\n* **Cách AI hoạt động:** Thu thập dữ liệu, Huấn luyện mô hình, Dự đoán kết quả.\n* **Ưu điểm của AI:** Tăng cường hiệu suất công việc, Cải thiện chất lượng sản phẩm và dịch vụ.\n* **Hạn chế của AI:** Rủi ro về việc mất việc làm, Mất dữ liệu và Mất kiểm soát.\n* **Ứng dụng thực tế của AI:** Giáo dục, Học tập, Đời sống.',0,'2026-05-08 05:30:13'),(25,6,'ưu điểm của ai','Dưới đây là một số ưu điểm của AI:\n\n1. **Tăng cường hiệu suất công việc:** AI có thể thực hiện các nhiệm vụ một cách nhanh chóng và chính xác hơn con người.\n2. **Cải thiện chất lượng sản phẩm và dịch vụ:** AI có thể giúp cải thiện chất lượng sản phẩm và dịch vụ bằng cách phân tích dữ liệu và đưa ra quyết định thông minh.\n3. **Tăng cường khả năng tự động hóa:** AI có thể tự động hóa các nhiệm vụ lặp đi lặp lại, giúp giảm thiểu thời gian và tăng cường hiệu suất.\n4. **Cải thiện chất lượng cuộc sống:** AI có thể giúp cải thiện chất lượng cuộc sống bằng cách cung cấp các dịch vụ và sản phẩm thông minh.\n5. **Tăng cường khả năng phân tích dữ liệu:** AI có thể giúp phân tích dữ liệu một cách nhanh chóng và chính xác hơn con người.\n6. **Tăng cường khả năng dự đoán và phòng ngừa:** AI có thể giúp dự đoán và phòng ngừa các sự kiện tiềm ẩn bằng cách phân tích dữ liệu và đưa ra quyết định thông minh.\n7. **Tăng cường khả năng tương tác:** AI có thể giúp tăng cường khả năng tương tác giữa con người và máy móc bằng cách cung cấp các giao diện người máy thông minh.\n8. **Cải thiện khả năng học hỏi:** AI có thể giúp cải thiện khả năng học hỏi của con người bằng cách cung cấp các mô hình học hỏi và phân tích dữ liệu.',0,'2026-05-08 05:31:07'),(26,6,'ưu điểm ai','Một số ưu điểm chính của AI:\n\n1. **Tăng cường hiệu suất công việc**\n2. **Cải thiện chất lượng sản phẩm và dịch vụ**\n3. **Tăng cường khả năng tự động hóa**\n4. **Cải thiện chất lượng cuộc sống**\n5. **Tăng cường khả năng phân tích dữ liệu**',0,'2026-05-08 05:37:00'),(27,6,'các loại ai phổ biến','Dưới đây là một số loại AI phổ biến:\n\n1. **AI phản hồi tự động (Chatbot):** Là loại AI được thiết kế để trả lời các câu hỏi và thực hiện các nhiệm vụ tự động.\n2. **Machine Learning (ML):** Là loại AI được thiết kế để học hỏi từ dữ liệu và cải thiện khả năng dự đoán và phân tích.\n3. **Deep Learning (DL):** Là loại AI được thiết kế để học hỏi từ dữ liệu và cải thiện khả năng phân tích và dự đoán bằng cách sử dụng các mô hình 神经 mạng.\n\nNhững loại AI này được sử dụng rộng rãi trong nhiều lĩnh vực như:\n\n* Xử lý ngôn ngữ tự nhiên\n* Phân tích hình ảnh và video\n* Dự đoán và phân tích dữ liệu\n* Tự động hóa các nhiệm vụ lặp đi lặp lại\n\nHy vọng thông tin này hữu ích!',0,'2026-05-08 05:37:23'),(28,6,'tóm tắt lại điểm chính của bài học','Bài học giới thiệu tổng quan về AI (Trí Tuệ Nhân Tạo), bao gồm:\n\n* Khái niệm và cách AI mô phỏng tư duy con người thông qua học dữ liệu và phân tích thông tin\n* Các loại AI phổ biến như AI phản hồi tự động, Machine Learning và Deep Learning\n* Cách AI hoạt động qua các bước thu thập dữ liệu, huấn luyện mô hình và dự đoán kết quả\n* Ưu điểm và hạn chế của AI\n* Các ứng dụng thực tế của AI trong giáo dục, học tập và đời sống hiện đại\n\nHy vọng thông tin này hữu ích!',0,'2026-05-08 05:38:13'),(29,17,'Tạo cho mình một bài tập Python cơ bản để luyện vòng lặp For.','Bài tập Python cơ bản về vòng lặp For:\n\n**Bài tập:**\nLập trình viên đang thiết kế một ứng dụng quản lý điểm của một trường học. Ứng dụng này yêu cầu phải thể hiện danh sách điểm của các học sinh trong một lớp học.\n\nDưới đây là dữ liệu điểm của các học sinh:\n\n- Học sinh 1: 8\n- Học sinh 2: 9\n- Học sinh 3: 7\n- Học sinh 4: 6\n- Học sinh 5: 8\n\nHãy viết một đoạn mã Python để in ra danh sách điểm của các học sinh.\n\n**Lời giải:**\n```python\n# Danh sách điểm của các học sinh\ndiem_hocsinh = [8, 9, 7, 6, 8]\n\n# Vòng lặp For để in danh sách điểm\nfor i in range(len(diem_hocsinh)):\n    print(f\"Học sinh {i+1}: {diem_hocsinh[i]}\")\n```\n\n**Yêu cầu:**\n\n- Hãy viết lại đoạn mã trên bằng cách sử dụng vòng lặp For với dữ liệu điểm của các học sinh.\n\n**Hướng dẫn:**\n\n- Sử dụng vòng lặp For để duyệt qua danh sách điểm của các học sinh.\n- Trong mỗi lần lặp, hãy in ra điểm của từng học sinh.\n\n**Lời giải:**\n```python\n# Danh sách điểm của các học sinh\ndiem_hocsinh = [8, 9, 7, 6, 8]\n\n# Vòng lặp For để in danh sách điểm\nfor diem in diem_hocsinh:\n    print(diem)\n```\nHoặc bạn cũng có thể sử dụng vòng lặp For với index như dưới đây:\n```python\n# Danh sách điểm của các học sinh\ndiem_hocsinh = [8, 9, 7, 6, 8]\n\n# Vòng lặp For để in danh sách điểm\nfor i, diem in enumerate(diem_hocsinh):\n    print(f\"Học sinh {i+1}: {diem}\")\n```\n\n**Kết quả:**\n- Mỗi lần chạy đoạn mã trên sẽ in ra danh sách điểm của các học sinh.',0,'2026-05-09 06:31:41'),(30,17,'Tạo cho mình một bài tập Python cơ bản để luyện vòng lặp For.','Bài tập Python cơ bản về vòng lặp For:\n\n**Bài tập:**\nLập trình viên đang thiết kế một ứng dụng quản lý thời gian của một ngày. Ứng dụng này yêu cầu phải thể hiện các giờ trong một ngày.\n\nHãy viết một đoạn mã Python để in ra các giờ trong một ngày (từ 0 đến 23).\n\n**Lời giải:**\n```python\n# Vòng lặp For để in các giờ trong một ngày\nfor gio in range(24):\n    print(gio)\n```\n\n**Yêu cầu:**\n\n- Hãy viết lại đoạn mã trên bằng cách sử dụng vòng lặp For và in ra các giờ dưới dạng chuỗi (chẳng hạn như \"0\", \"1\", \"2\", ...).\n\n**Hướng dẫn:**\n\n- Sử dụng vòng lặp For để duyệt qua các giờ trong một ngày.\n- Trong mỗi lần lặp, hãy in ra giờ dưới dạng chuỗi.\n\n**Lời giải:**\n```python\n# Vòng lặp For để in các giờ trong một ngày\nfor gio in range(24):\n    print(str(gio))\n```\nHoặc bạn cũng có thể sử dụng vòng lặp For với hàm format để in ra các giờ dưới dạng chuỗi:\n```python\n# Vòng lặp For để in các giờ trong một ngày\nfor gio in range(24):\n    print(\"{}:00\".format(gio))\n```\nHoặc bạn cũng có thể sử dụng vòng lặp For với hàm f-string để in ra các giờ dưới dạng chuỗi:\n```python\n# Vòng lặp For để in các giờ trong một ngày\nfor gio in range(24):\n    print(f\"{gio}:00\")\n```\n\n**Kết quả:**\n- Mỗi lần chạy đoạn mã trên sẽ in ra các giờ trong một ngày dưới dạng chuỗi.',0,'2026-05-10 06:32:26'),(31,17,'hệ thống có khóa học nào','Hiện tại, hệ thống có các khóa học sau:\n\n1. **Khóa học: Lập trình Java cơ bản** (Cấp độ: beginner) - Tóm tắt: Học Java từ cơ bản đến nâng cao...\n2. **Khóa học: SQL cho người mới** (Cấp độ: beginner) - Tóm tắt: Học SQL từ đầu...\n3. **Khóa học: Test 1** (Cấp độ: beginner) - Tóm tắt: test...\n4. **Khóa học: Nhập môn lập trình** (Cấp độ: beginner) - Tóm tắt: Nội dung thường bao gồm: \n + Khái niệm về lập trình và ngôn ngữ lập trình  \n + Biến, kiểu dữ liệu, toán tử  \n + Cấu trúc điều kiện (if, switch)  \n + Vòng lặp ... \n5. **Khóa học: abc** (Cấp độ: intermediate) - Tóm tắt: bbbbb...\n\nNếu bạn muốn học thêm, hãy tham khảo các khóa học trên!',0,'2026-05-10 06:37:30'),(32,17,'tóm tắt nội dung cho tôi','Tôi xin lỗi vì trước đó tôi đã trả lời sai về danh sách khóa học.\n\nDưới đây là danh sách khóa học hiện có trong hệ thống:\n\n1. **Khóa học: SQL cho người mới** (Cấp độ: beginner) - Tóm tắt: Học SQL từ đầu...\n2. **Khóa học: Nhập môn lập trình** (Cấp độ: beginner) - Tóm tắt: Nội dung thường bao gồm: \n + Khái niệm về lập trình và ngôn ngữ lập trình  \n + Biến, kiểu dữ liệu, toán tử  \n + Cấu trúc điều kiện (if, switch)  \n + Vòng lặp ... \n3. **Khóa học: learning AI** (Cấp độ: advanced) - Tóm tắt: Hiểu về AI sâu hơn...\n\nHy vọng thông tin này hữu ích cho bạn!',0,'2026-05-10 08:38:32'),(33,17,'tóm tắt nội dung tôi đang học cho tôi','Bạn đang học khóa học **Nhập môn lập trình** (Cấp độ: beginner) và đang xem phần **Giới thiệu về lập trình - Chương 1: Giới thiệu**.\n\nTóm tắt nội dung của chương này như sau:\n\n* Giới thiệu về ngôn ngữ lập trình và quá trình lập trình\n* Khái niệm về ngôn ngữ máy và ngôn ngữ lập trình thế hệ 1 (1GL)\n* Khái niệm về ngôn ngữ lập trình thế hệ 2 (2GL) - hợp ngữ (assembly languages)\n* Khái niệm về ngôn ngữ lập trình thế hệ 3 (3GL) - ngôn ngữ bậc cao (high-level programming languages)\n* Định nghĩa ngôn ngữ lập trình và các yêu cầu cơ bản\n* Khái niệm về chương trình, mã nguồn và quá trình dịch\n\nHy vọng thông tin này hữu ích cho bạn!',0,'2026-05-10 08:38:59'),(34,17,'gợi ý cho khóa học về lập trình','Nếu bạn đang tìm kiếm các khóa học về lập trình, tôi có thể gợi ý một số khóa học trong hệ thống như sau:\n\n1. **Khóa học: Nhập môn lập trình** (Cấp độ: beginner) - Tóm tắt: Nội dung thường bao gồm: \n + Khái niệm về lập trình và ngôn ngữ lập trình  \n + Biến, kiểu dữ liệu, toán tử  \n + Cấu trúc điều kiện (if, switch)  \n + Vòng lặp ... \n2. **Khóa học: Lập trình cơ bản với Python** (Cấp độ: beginner) - Tóm tắt: Học lập trình cơ bản với ngôn ngữ Python\n3. **Khóa học: Lập trình web với HTML, CSS, JavaScript** (Cấp độ: beginner) - Tóm tắt: Học lập trình web với HTML, CSS và JavaScript\n4. **Khóa học: Lập trình hướng đối tượng với Java** (Cấp độ: intermediate) - Tóm tắt: Học lập trình hướng đối tượng với ngôn ngữ Java\n5. **Khóa học: Lập trình di động với Java hoặc Kotlin** (Cấp độ: intermediate) - Tóm tắt: Học lập trình di động với ngôn ngữ Java hoặc Kotlin\n\nHy vọng thông tin này hữu ích cho bạn!',0,'2026-05-10 08:48:06'),(35,17,'giới thiệu khóa học','Tôi có thể giới thiệu cho bạn một số khóa học trong hệ thống như sau:\n\n**Khóa học: Nhập môn lập trình**\n\n* **Mô tả khóa học**: Khóa học này giúp bạn hiểu về ngôn ngữ lập trình, biến, kiểu dữ liệu, toán tử, cấu trúc điều kiện, vòng lặp và các khái niệm cơ bản khác về lập trình.\n* **Nội dung khóa học**: Khóa học này bao gồm các chủ đề như:\n + Khái niệm về lập trình và ngôn ngữ lập trình\n + Biến, kiểu dữ liệu, toán tử\n + Cấu trúc điều kiện (if, switch)\n + Vòng lặp\n + Các khái niệm khác về lập trình\n* **Cấp độ**: Khóa học này dành cho người mới bắt đầu với lập trình, cấp độ beginner.\n* **Mục tiêu**: Sau khi hoàn thành khóa học này, bạn sẽ có kiến thức cơ bản về lập trình và có thể tự học các ngôn ngữ lập trình khác.\n\n**Khóa học: Lập trình cơ bản với Python**\n\n* **Mô tả khóa học**: Khóa học này giúp bạn học lập trình cơ bản với ngôn ngữ Python.\n* **Nội dung khóa học**: Khóa học này bao gồm các chủ đề như:\n + Giới thiệu về Python\n + Biến, kiểu dữ liệu, toán tử\n + Cấu trúc điều kiện (if, switch)\n + Vòng lặp\n + Các khái niệm khác về lập trình với Python\n* **Cấp độ**: Khóa học này dành cho người mới bắt đầu với lập trình, cấp độ beginner.\n* **Mục tiêu**: Sau khi hoàn thành khóa học này, bạn sẽ có kiến thức cơ bản về lập trình với Python và có thể tự học các ngôn ngữ lập trình khác.\n\n**Khóa học: Lập trình web với HTML, CSS, JavaScript**\n\n* **Mô tả khóa học**: Khóa học này giúp bạn học lập trình web với HTML, CSS và JavaScript.\n* **Nội dung khóa học**: Khóa học này bao gồm các chủ đề như:\n + Giới thiệu về HTML\n + Giới thiệu về CSS\n + Giới thiệu về JavaScript\n + Lập trình web với HTML, CSS và JavaScript\n* **Cấp độ**: Khóa học này dành cho người mới bắt đầu với lập trình web, cấp độ beginner.\n* **Mục tiêu**: Sau khi hoàn thành khóa học này, bạn sẽ có kiến thức cơ bản về lập trình web với HTML, CSS và JavaScript và có thể tự học các ngôn ngữ lập trình khác.\n\nHy vọng thông tin này hữu ích cho bạn!',0,'2026-05-10 09:39:33'),(36,17,'giới thiệu các khóa học của hệ thống','Tôi có thể giới thiệu cho bạn các khóa học trong hệ thống như sau:\n\n**Khóa học: SQL cho người mới**\n\n* **Mô tả khóa học**: Khóa học này giúp bạn học SQL từ đầu, bao gồm các khái niệm cơ bản về SQL, cách sử dụng SQL để lưu trữ, quản lý và phân tích dữ liệu.\n* **Nội dung khóa học**: Khóa học này bao gồm các chủ đề như:\n + Introduction to SQL\n + Cú pháp và cấu trúc của SQL\n + Tạo và quản lý bảng\n + Chèn, sửa, xóa dữ liệu\n + Tìm kiếm và sắp xếp dữ liệu\n + Sử dụng hàm và tích hợp SQL\n* **Cấp độ**: Khóa học này dành cho người mới bắt đầu với SQL, cấp độ beginner.\n* **Mục tiêu**: Sau khi hoàn thành khóa học này, bạn sẽ có kiến thức cơ bản về SQL và có thể tự học các khái niệm liên quan.\n\n**Khóa học: Nhập môn lập trình**\n\n* **Mô tả khóa học**: Khóa học này giúp bạn hiểu về ngôn ngữ lập trình, biến, kiểu dữ liệu, toán tử, cấu trúc điều kiện, vòng lặp và các khái niệm cơ bản khác về lập trình.\n* **Nội dung khóa học**: Khóa học này bao gồm các chủ đề như:\n + Khái niệm về lập trình và ngôn ngữ lập trình\n + Biến, kiểu dữ liệu, toán tử\n + Cấu trúc điều kiện (if, switch)\n + Vòng lặp\n + Các khái niệm khác về lập trình\n* **Cấp độ**: Khóa học này dành cho người mới bắt đầu với lập trình, cấp độ beginner.\n* **Mục tiêu**: Sau khi hoàn thành khóa học này, bạn sẽ có kiến thức cơ bản về lập trình và có thể tự học các ngôn ngữ lập trình khác.\n\n**Khóa học: Lập trình cơ bản với Python**\n\n* **Mô tả khóa học**: Khóa học này giúp bạn học lập trình cơ bản với ngôn ngữ Python.\n* **Nội dung khóa học**: Khóa học này bao gồm các chủ đề như:\n + Giới thiệu về Python\n + Biến, kiểu dữ liệu, toán tử\n + Cấu trúc điều kiện (if, switch)\n + Vòng lặp\n + Các khái niệm khác về lập trình với Python\n* **Cấp độ**: Khóa học này dành cho người mới bắt đầu với lập trình, cấp độ beginner.\n* **Mục tiêu**: Sau khi hoàn thành khóa học này, bạn sẽ có kiến thức cơ bản về lập trình với Python và có thể tự học các ngôn ngữ lập trình khác.\n\n**Khóa học: Lập trình web với HTML, CSS, JavaScript**\n\n* **Mô tả khóa học**: Khóa học này giúp bạn học lập trình web với HTML, CSS và JavaScript.\n* **Nội dung khóa học**: Khóa học này bao gồm các chủ đề như:\n + Giới thiệu về HTML\n + Giới thiệu về CSS\n + Giới thiệu về JavaScript\n + Lập trình web với HTML, CSS và JavaScript\n* **Cấp độ**: Khóa học này dành cho người mới bắt đầu với lập trình web, cấp độ beginner.\n* **Mục tiêu**: Sau khi hoàn thành khóa học này, bạn sẽ có kiến thức cơ bản về lập trình web với HTML, CSS và JavaScript và có thể tự học các ngôn ngữ lập trình khác.\n\n**Khóa học: Learning AI**\n\n* **Mô tả khóa học**: Khóa học này giúp bạn hiểu về AI sâu hơn, bao gồm các khái niệm cơ bản về AI, cách sử dụng AI để giải quyết các vấn đề thực tế.\n* **Nội dung khóa học**: Khóa học này bao gồm các chủ đề như:\n + Introduction to AI\n + Các khái niệm cơ bản về AI\n + Cách sử dụng AI để giải quyết các vấn đề thực tế\n + Các công nghệ AI hiện đại\n* **Cấp độ**: Khóa học này dành cho người đã có kinh nghiệm với lập trình và muốn học về AI, cấp độ advanced.\n* **Mục tiêu**: Sau khi hoàn thành khóa học này, bạn sẽ có kiến thức sâu về AI và có thể tự học các khái niệm liên quan.\n\nHy vọng thông tin này hữu ích cho bạn!',0,'2026-05-10 09:40:04');
/*!40000 ALTER TABLE `ai_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `answers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_id` int DEFAULT NULL,
  `answer_text` varchar(255) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_question` (`question_id`),
  KEY `idx_question_id` (`question_id`),
  KEY `fk_answers_deleted_by` (`deleted_by`),
  CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
  CONSTRAINT `fk_answers_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answers`
--

LOCK TABLES `answers` WRITE;
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
INSERT INTO `answers` VALUES (1,1,'Ngôn ngữ lập trình',1,NULL,NULL),(2,1,'Hệ điều hành',0,NULL,NULL),(3,2,'Lấy dữ liệu',1,NULL,NULL),(4,2,'Xóa dữ liệu',0,NULL,NULL),(5,3,'Máy tự sửa chữa',0,NULL,NULL),(6,3,'Máy học từ dữ liệu',1,NULL,NULL),(7,3,'Máy tính nhanh hơn',0,NULL,NULL),(8,3,'Phần mềm diệt virus',0,NULL,NULL),(9,4,'AI xử lý ngôn ngữ tự nhiên',1,NULL,NULL),(10,4,'AI nhận diện hình ảnh',0,NULL,NULL),(11,4,'AI lái xe tự động',0,NULL,NULL),(12,4,'AI chơi game',0,NULL,NULL),(13,5,'Máy tự sửa chữa',0,NULL,NULL),(14,5,'Máy học từ dữ liệu',1,NULL,NULL),(15,5,'Máy tính nhanh hơn',0,NULL,NULL),(16,5,'Phần mềm diệt virus',0,NULL,NULL),(17,6,'AI xử lý ngôn ngữ tự nhiên',1,NULL,NULL),(18,6,'AI nhận diện hình ảnh',0,NULL,NULL),(19,6,'AI lái xe tự động',0,NULL,NULL),(20,6,'AI chơi game',0,NULL,NULL),(21,7,'Automatic Internet',0,NULL,NULL),(22,7,'Advanced Information',0,NULL,NULL),(23,7,'Artificial Internet',0,NULL,NULL),(24,7,'Artificial Intelligence',1,NULL,NULL);
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificates`
--

DROP TABLE IF EXISTS `certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `certificates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `course_id` int NOT NULL,
  `certificate_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completion_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score` decimal(5,2) DEFAULT NULL COMMENT 'Average quiz score',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificate_code` (`certificate_code`),
  UNIQUE KEY `uq_user_course_cert` (`user_id`,`course_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificates`
--

LOCK TABLES `certificates` WRITE;
/*!40000 ALTER TABLE `certificates` DISABLE KEYS */;
INSERT INTO `certificates` VALUES (1,17,5,'21829818-abd4-44d5-8f4f-a226f151ab53','2026-05-10 06:52:30',100.00,'2026-05-10 06:52:30'),(2,17,7,'a4ca29e9-2173-409e-b684-1d5a915cdbc7','2026-05-10 08:46:54',100.00,'2026-05-10 08:46:54');
/*!40000 ALTER TABLE `certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chapters`
--

DROP TABLE IF EXISTS `chapters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chapters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `order_index` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_course` (`course_id`),
  KEY `fk_chapters_deleted_by` (`deleted_by`),
  KEY `idx_course_order` (`course_id`,`order_index`),
  KEY `idx_chapters_course` (`course_id`),
  CONSTRAINT `chapters_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_chapters_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chapters`
--

LOCK TABLES `chapters` WRITE;
/*!40000 ALTER TABLE `chapters` DISABLE KEYS */;
INSERT INTO `chapters` VALUES (1,1,'Chương 1: Giới thiệu Java',1,'2026-04-16 15:21:57',NULL,NULL),(2,1,'Chương 2: Biến và kiểu dữ liệu',2,'2026-04-16 15:21:57',NULL,NULL),(3,2,'Chương 1: SQL cơ bản',1,'2026-04-16 15:21:57',NULL,NULL),(4,3,'1',1,'2026-04-20 16:44:24',NULL,NULL),(5,5,'Chương 1: Giới thiệu',0,'2026-05-07 06:33:27',NULL,NULL),(6,6,'1',0,'2026-05-07 07:26:11',NULL,NULL),(7,7,'Giới thiệu về AI',0,'2026-05-08 03:43:00',NULL,NULL),(8,7,'Chương 2: Xây dựng AI',1,'2026-05-10 07:21:51',NULL,NULL);
/*!40000 ALTER TABLE `chapters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_categories`
--

DROP TABLE IF EXISTS `course_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Font Awesome icon class',
  `parent_id` int DEFAULT NULL,
  `order_index` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `course_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `course_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_categories`
--

LOCK TABLES `course_categories` WRITE;
/*!40000 ALTER TABLE `course_categories` DISABLE KEYS */;
INSERT INTO `course_categories` VALUES (1,'Lập trình','lap-trinh','fa-code',NULL,1),(2,'Khoa học dữ liệu','khoa-hoc-du-lieu','fa-chart-bar',NULL,2),(3,'Trí tuệ nhân tạo','tri-tue-nhan-tao','fa-robot',NULL,3),(4,'Thiết kế','thiet-ke','fa-paint-brush',NULL,4),(5,'Ngoại ngữ','ngoai-ngu','fa-language',NULL,5),(6,'Toán học','toan-hoc','fa-calculator',NULL,6),(7,'Kinh doanh','kinh-doanh','fa-briefcase',NULL,7),(8,'Khác','khac','fa-folder',NULL,99);
/*!40000 ALTER TABLE `course_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_payments`
--

DROP TABLE IF EXISTS `course_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `course_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_ref` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'vietqr',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `course_payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_payments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_payments`
--

LOCK TABLES `course_payments` WRITE;
/*!40000 ALTER TABLE `course_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_reviews`
--

DROP TABLE IF EXISTS `course_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `course_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `course_reviews_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_reviews`
--

LOCK TABLES `course_reviews` WRITE;
/*!40000 ALTER TABLE `course_reviews` DISABLE KEYS */;
INSERT INTO `course_reviews` VALUES (3,8,1,5,'Khóa học rất hay','2026-04-17 03:09:19'),(4,9,1,4,'Dễ hiểu','2026-04-17 03:09:19'),(5,1,7,5,'Khóa học tuyệt vời!','2026-05-10 09:00:03'),(6,17,7,5,'hay','2026-05-10 09:06:25'),(7,17,5,5,'hay','2026-05-10 09:31:01');
/*!40000 ALTER TABLE `course_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `teacher_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `estimated_duration` int NOT NULL DEFAULT '0' COMMENT 'Tổng thời lượng ước tính (phút)',
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `thumbnail` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00',
  `is_premium` tinyint(1) DEFAULT '0',
  `status` enum('draft','pending','approved','published','hidden') DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  `total_lessons` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_teacher` (`teacher_id`),
  KEY `idx_status` (`status`),
  KEY `deleted_by` (`deleted_by`),
  KEY `idx_deleted_at` (`deleted_at`),
  KEY `category_id` (`category_id`),
  KEY `idx_courses_teacher` (`teacher_id`),
  KEY `idx_courses_status` (`status`),
  CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `courses_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `course_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,2,NULL,'beginner',0,'Lập trình Java cơ bản','Học Java từ cơ bản đến nâng cao',NULL,0.00,0,'approved','2026-04-16 15:21:57',NULL,'2026-05-10 08:29:19',NULL,2),(2,2,NULL,'beginner',0,'SQL cho người mới','Học SQL từ đầu',NULL,0.00,1,'approved','2026-04-16 15:21:57',NULL,NULL,NULL,1),(3,7,NULL,'beginner',0,'abc','aaaaaaaaaaa','/uploads/courses/2026_04/file_69e656ec93e5e_1776703212.mp4',5000.00,1,'approved','2026-04-20 16:40:12',NULL,'2026-05-07 07:21:50',NULL,0),(4,7,NULL,'beginner',0,'Test 1','test ','/uploads/courses/2026_04/file_69ea3ce31feff_1776958691.png',10000.00,1,'approved','2026-04-23 15:38:11',NULL,'2026-05-10 08:29:06',NULL,0),(5,7,1,'beginner',60,'Nhập môn lập trình','Nội dung thường bao gồm:\n•	Khái niệm về lập trình và ngôn ngữ lập trình \n•	Biến, kiểu dữ liệu, toán tử \n•	Cấu trúc điều kiện (if, switch) \n•	Vòng lặp (for, while) \n•	Hàm (Function) \n•	Mảng, chuỗi \n•	Thuật toán cơ bản và tư duy giải quyết vấn đề \n•	Làm việc với input/output \n•	Thực hành viết các chương trình đơn giản \nSau khóa học, học viên có thể:\n•	Hiểu cách hoạt động của chương trình \n•	Viết được các ứng dụng cơ bản \n•	Rèn luyện tư duy logic \n•	Làm nền tảng để học các công nghệ nâng cao như Web, Mobile, AI, Java, PHP, Python,... \nPhù hợp cho:\n•	Người mới bắt đầu \n•	Sinh viên CNTT \n•	Người muốn chuyển ngành sang lập trình \n•	Học sinh muốn tiếp cận công nghệ sớm. \n','/uploads/courses/2026_05/img_69fc321a24a18_1778135578.png',0.00,0,'approved','2026-05-07 06:32:58',NULL,NULL,NULL,2),(6,7,2,'intermediate',5,'abc','bbbbb','/uploads/courses/2026_05/img_69fc3e6a0207a_1778138730.png',5000.00,1,'hidden','2026-05-07 07:25:30',NULL,'2026-05-10 08:27:34',NULL,1),(7,16,3,'advanced',30,'learning AI','Hiểu về AI sâu hơn','/uploads/courses/2026_05/img_69fd5b77e730a_1778211703.png',10000.00,1,'approved','2026-05-08 03:41:44',NULL,NULL,NULL,3);
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `enrolled_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `progress_percent` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`course_id`),
  UNIQUE KEY `uq_enrollment` (`user_id`,`course_id`),
  UNIQUE KEY `uq_enrollment_user_course` (`user_id`,`course_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_course` (`course_id`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` VALUES (4,8,1,'2026-04-16 15:26:02',50),(5,8,2,'2026-04-16 15:26:02',0),(6,9,1,'2026-04-16 15:26:02',100),(7,10,1,'2026-04-24 13:39:53',0),(8,8,3,'2026-04-25 03:04:58',0),(9,11,3,'2026-04-25 03:06:46',0),(10,12,1,'2026-04-25 04:12:15',0),(11,8,5,'2026-05-07 07:08:40',0),(12,8,6,'2026-05-07 07:31:49',0),(13,17,5,'2026-05-10 05:44:47',100),(14,17,7,'2026-05-10 08:40:16',100);
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_path_courses`
--

DROP TABLE IF EXISTS `learning_path_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `learning_path_courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `path_id` int NOT NULL,
  `course_id` int NOT NULL,
  `order_index` int NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=bắt buộc, 0=tùy chọn',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_path_course` (`path_id`,`course_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `learning_path_courses_ibfk_1` FOREIGN KEY (`path_id`) REFERENCES `learning_paths` (`id`) ON DELETE CASCADE,
  CONSTRAINT `learning_path_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_path_courses`
--

LOCK TABLES `learning_path_courses` WRITE;
/*!40000 ALTER TABLE `learning_path_courses` DISABLE KEYS */;
/*!40000 ALTER TABLE `learning_path_courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_path_enrollments`
--

DROP TABLE IF EXISTS `learning_path_enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `learning_path_enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `path_id` int NOT NULL,
  `progress_percent` int NOT NULL DEFAULT '0',
  `enrolled_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_path` (`user_id`,`path_id`),
  KEY `path_id` (`path_id`),
  CONSTRAINT `learning_path_enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `learning_path_enrollments_ibfk_2` FOREIGN KEY (`path_id`) REFERENCES `learning_paths` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_path_enrollments`
--

LOCK TABLES `learning_path_enrollments` WRITE;
/*!40000 ALTER TABLE `learning_path_enrollments` DISABLE KEYS */;
/*!40000 ALTER TABLE `learning_path_enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_paths`
--

DROP TABLE IF EXISTS `learning_paths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `learning_paths` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty` enum('beginner','intermediate','advanced') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'beginner',
  `estimated_hours` int NOT NULL DEFAULT '0',
  `created_by` int NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `learning_paths_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_paths`
--

LOCK TABLES `learning_paths` WRITE;
/*!40000 ALTER TABLE `learning_paths` DISABLE KEYS */;
/*!40000 ALTER TABLE `learning_paths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_progress`
--

DROP TABLE IF EXISTS `lesson_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson_progress` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `lesson_id` int DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`lesson_id`),
  UNIQUE KEY `uq_lesson_progress` (`user_id`,`lesson_id`),
  UNIQUE KEY `uq_lesson_progress_user_lesson` (`user_id`,`lesson_id`),
  KEY `idx_user` (`user_id`),
  KEY `lesson_id` (`lesson_id`),
  KEY `idx_user_lesson` (`user_id`,`lesson_id`),
  KEY `idx_user_completed` (`user_id`,`is_completed`),
  KEY `idx_progress_completed` (`user_id`,`is_completed`,`completed_at`),
  CONSTRAINT `lesson_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `lesson_progress_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_progress`
--

LOCK TABLES `lesson_progress` WRITE;
/*!40000 ALTER TABLE `lesson_progress` DISABLE KEYS */;
INSERT INTO `lesson_progress` VALUES (1,10,1,1,'2026-04-24 13:40:01'),(2,10,2,1,'2026-04-24 13:40:16'),(3,8,4,1,'2026-05-07 07:21:09'),(4,6,6,1,'2026-05-07 07:28:28'),(5,6,8,1,'2026-05-08 05:16:20'),(6,17,4,1,'2026-05-10 06:38:21'),(7,17,5,1,'2026-05-10 06:38:24'),(12,17,9,1,'2026-05-10 08:46:37'),(13,17,10,1,'2026-05-10 08:46:46'),(14,17,8,1,'2026-05-10 08:46:54');
/*!40000 ALTER TABLE `lesson_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lessons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `chapter_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content_type` enum('video','text','quiz') NOT NULL DEFAULT 'video',
  `content` longtext,
  `objectives` text,
  `ai_summary` text,
  `video_url` varchar(255) DEFAULT NULL,
  `duration` int NOT NULL DEFAULT '0' COMMENT 'Thời lượng video (giây)',
  `video_filename` varchar(255) DEFAULT NULL COMMENT 'Tên file video thật trên disk (UUID)',
  `video_size` bigint NOT NULL DEFAULT '0' COMMENT 'Kích thước file video (bytes)',
  `order_index` int DEFAULT NULL,
  `is_free` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_chapter` (`chapter_id`),
  KEY `fk_lessons_deleted_by` (`deleted_by`),
  KEY `idx_chapter_order` (`chapter_id`,`order_index`),
  KEY `idx_lessons_chapter` (`chapter_id`),
  CONSTRAINT `fk_lessons_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lessons`
--

LOCK TABLES `lessons` WRITE;
/*!40000 ALTER TABLE `lessons` DISABLE KEYS */;
INSERT INTO `lessons` VALUES (1,1,'Java là gì?','video','Giới thiệu Java',NULL,NULL,'video1.mp4',0,NULL,0,1,1,'2026-04-16 15:21:57',NULL,NULL),(2,2,'Biến trong Java','video','Học về biến',NULL,NULL,'video2.mp4',0,NULL,0,1,0,'2026-04-16 15:21:57',NULL,NULL),(3,3,'SELECT cơ bản','video','Học SELECT',NULL,NULL,'video3.mp4',0,NULL,0,1,1,'2026-04-16 15:21:57',NULL,NULL),(4,5,'Giới thiệu về lập trình','video','Trước hết dạng chương trình duy nhất mà máy tính có thể thực thi trực tiếp là ngôn ngữ máy hay mã máy. Nó có dạng dãy các số nhị phân, thường được ghép nhóm thành byte 8 bit cho các hệ xử lý 8/16/32/64 bit [note 1]. Nội dung byte thường biểu diễn bằng đôi số hex. Để có được bộ mã này ngày nay người ta dùng ngôn ngữ lập trình để viết ra chương trình ở dạng văn bản và dùng trình dịch để chuyển sang mã máy [1].\nKhi kỹ thuật điện toán ra đời chưa có ngôn ngữ lập trình dạng đại diện nào, thì phải lập trình trực tiếp bằng mã máy. Dãy byte viết ra được đục lỗ lên phiếu đục lỗ (punched card) và nhập qua máy đọc phiếu tới máy tính [2]. Sau đó chương trình có thể được ghi vào băng/đĩa từ để sau này nhập nhanh vào máy tính. Ngôn ngữ máy được gọi là \"ngôn ngữ lập trình thế hệ 1\" (1GL, first-generation programming languages) [3].\nSau đó các mã lệnh được thay thế bằng các tên gợi nhớ và lập trình được ở dạng văn bản (text) rồi dịch sang mã máy. Hợp ngữ (assembly languages) ra đời, là \"ngôn ngữ lập trình thế hệ 2\" (2GL, second-generation programming languages). Lập trình thuận lợi hơn, khi dịch có thể liên kết với thư viện chương trình con ở cả dạng macro (đoạn chưa dịch) và lẫn mã đã dịch. Hợp ngữ hiện được dùng là ngôn ngữ bậc thấp (low-level programming languages) để tinh chỉnh ngôn ngữ bậc cao thực hiện truy nhập trực tiếp phần cứng cụ thể trong việc lập trình hệ thống, tạo các hiệu ứng đặc biệt cho chương trình.\nNgôn ngữ bậc cao (high-level programming languages) hay \"ngôn ngữ lập trình thế hệ 3\" (3GL, third-generation programming languages) ra đời vào những năm 1950. Đây là các ngôn ngữ hình thức, dùng trong lập trình máy điện toán và không lệ thuộc vào hệ máy tính cụ thể nào. Nó giải phóng người lập trình ứng dụng làm việc trong hệ điều hành xác định mà không phải quan tâm đến phần cứng cụ thể. Các ngôn ngữ được phát triển liên tục với các dạng và biến thể mới, theo bước phát triển của kỹ thuật điện toán [4].\nĐối với ngôn ngữ bậc cao thì định nghĩa ngôn ngữ lập trình theo [Loud 94], T.3 là:\nNgôn ngữ lập trình là một hệ thống được ký hiệu hóa để miêu tả những tính toán (qua máy tính) trong một dạng mà cả con người và máy đều có thể đọc và hiểu được.\nTheo định nghĩa ở trên thì một ngôn ngữ lập trình phải thỏa mãn được hai điều kiện cơ bản sau:\nDễ hiểu và dễ sử dụng đối với người lập trình, để có thể dùng để giải quyết nhiều bài toán khác nhau.\nMiêu tả một cách đầy đủ và rõ ràng các tiến trình (tiếng Anh: process), để chạy được trên các hệ máy tính khác nhau.\nMột tập hợp các chỉ thị được biểu thị qua ngôn ngữ lập trình nhằm mục đích thực hiện các thao tác máy tính nào đó được gọi là một chương trình. Khái niệm này còn có những tên khác như chương trình máy tính hay chương trình điện toán.\nLưu ý: chương trình được viết cho máy vi tính thường được gọi là phần mềm máy tính. Ví dụ: chương trình Microsoft Word là một cách gọi chung chung; cách gọi phần mềm Microsoft Word chỉ rõ hơn nó là một chương trình ứng dụng.\nKhái niệm lập trình dùng để chỉ quá trình con người tạo ra chương trình máy tính thông qua ngôn ngữ lập trình. Người ta còn gọi đó là quá trình mã hoá thông tin tự nhiên thành ngôn ngữ máy. Từ viết mã cũng được dùng trong nhiều trường hợp để chỉ cùng một ý.\nNhư vậy, theo định nghĩa, mỗi ngôn ngữ lập trình cũng chính là một chương trình, nhưng nó có thể được dùng để tạo nên các chương trình khác. Văn bản được viết bằng ngôn ngữ lập trình để tạo nên chương trình được gọi là mã nguồn.\nThao tác chuyển đổi từ mã nguồn thành chuỗi các chỉ thị máy tính được thực hiện tương tự như việc chuyển đổi qua lại giữa các ngôn ngữ tự nhiên của con người. Các thao tác này gọi là biên dịch, hay ngắn gọn hơn là dịch. Nếu quá trình dịch diễn ra đồng thời với quá trình thực thi, ta gọi đó là thông dịch; nếu diễn ra trước, ta gọi đó là biên dịch. Phần mềm dịch tương ứng được gọi là phần mềm thông dịch và phần mềm biên dịch.\nMột phần mềm thông dịch là một phần mềm có khả năng đọc, chuyển mã nguồn của một ngôn ngữ và ra lệnh cho máy tính tiến hành các tính toán dựa theo cú pháp của ngôn ngữ.\nMột phần mềm biên dịch hay ngắn gọn hơn trình biên dịch là phần mềm có khả năng chuyển mã nguồn của một ngôn ngữ ban đầu sang dạng mã mới thường là một ngôn ngữ cấp thấp hơn.\nNgôn ngữ cấp thấp nhất là một chuỗi các chỉ thị máy tính mà có thể được thực thi trực tiếp bởi máy tính (thông qua các theo tác trên vùng nhớ). Trước đây, hầu hết các trình biên dịch cũ phải dịch từ mã nguồn sang bộ mã phụ (các tệp có dang *.obj) rồi mới tạo ra tập tin thực thi. Ngày nay, hầu hết các trình biên dịch đều có khả năng dịch mã nguồn trực tiếp thành các tập tin thực thi hay thành các dạng mã khác thấp hơn, tuỳ theo yêu cầu của người lập trình.\nĐiểm khác nhau giữa thông dịch và biên dịch là: trình thông dịch dịch từng câu lệnh theo yêu cầu thực thi và chương trình đích vừa tạo ra sẽ không được lưu lại; trong khi đó, trình biên dịch sẽ dịch toàn bộ chương trình, cho ra chương trình đích được lưu lại trong máy tính rồi mới thực hiện chương trình.\nMột chương trình máy tính có thể được thực thi bằng cách biên dịch, thông dịch, hoặc phối hợp cả hai.\nĐể đạt được yêu cầu về độ chính xác và tính hiệu quả, mã viết ra nhiều khi khó đọc ngay cả với chính người viết ra mã đó, chưa kể tới người khác. Chính vì lý do đó, mọi tài liệu, hướng dẫn lập trình đều khuyên nên thêm các chú giải vào mã nguồn trong quá trình viết. Các chú giải giúp người khác rất nhiều trong việc đọc hiểu mã nguồn; đối với chương trình phức tạp, chú giải là thành phần vô cùng quan trọng trong mã nguồn.\n',NULL,NULL,'',0,'5/5/a5b431750fc234bdf48d880450597140.mp4',0,1,1,'2026-05-07 06:36:30',NULL,NULL),(5,5,'abc','video','abc',NULL,NULL,'',0,'',0,2,0,'2026-05-07 07:23:32',NULL,NULL),(6,6,'aaa','video','aaaa',NULL,NULL,'',0,'6/6/1b00646e5519e8410d022758ab1d07aa.mp4',0,1,0,'2026-05-07 07:27:21',NULL,NULL),(7,7,'Giới thiệu về AI','text','Giới thiệu về AI (Trí Tuệ Nhân Tạo)\nAI là gì?\nAI (Artificial Intelligence - Trí Tuệ Nhân Tạo) là công nghệ giúp máy tính có khả năng mô phỏng tư duy và hành vi của con người. AI có thể học từ dữ liệu, phân tích thông tin và đưa ra quyết định tự động.\nHiện nay AI được ứng dụng rất rộng rãi trong:\n•	Chatbot hỗ trợ khách hàng \n•	Nhận diện khuôn mặt \n•	Dịch ngôn ngữ \n•	Gợi ý video trên TikTok, YouTube \n•	Xe tự lái \n•	Hỗ trợ học tập và giáo dục \nCác loại AI phổ biến\n1. AI phản hồi tự động\nLà dạng AI có thể trả lời câu hỏi hoặc thực hiện tác vụ theo dữ liệu được huấn luyện.\nVí dụ:\n•	ChatGPT \n•	Siri \n•	Google Assistant \n2. Machine Learning\nMáy tính học từ dữ liệu để cải thiện kết quả theo thời gian mà không cần lập trình quá chi tiết.\nVí dụ:\n•	Gợi ý phim trên Netflix \n•	Đề xuất sản phẩm Shopee \n3. Deep Learning\nMột nhánh nâng cao của Machine Learning sử dụng mạng nơ-ron để xử lý dữ liệu phức tạp như hình ảnh, giọng nói và video.\nAI hoạt động như thế nào?\nAI thường hoạt động qua các bước:\n1.	Thu thập dữ liệu \n2.	Huấn luyện mô hình \n3.	Phân tích dữ liệu \n4.	Đưa ra kết quả hoặc dự đoán \nVí dụ:\n•	AI học hàng triệu hình ảnh mèo để nhận biết đâu là mèo.\nƯu điểm của AI\n•	Tự động hóa công việc \n•	Tiết kiệm thời gian \n•	Xử lý dữ liệu nhanh \n•	Hỗ trợ học tập và nghiên cứu \n•	Cá nhân hóa trải nghiệm người dùng\nHạn chế và rủi ro của AI\n•	Có thể đưa ra thông tin sai \n•	Phụ thuộc dữ liệu huấn luyện \n•	Nguy cơ lạm dụng Deepfake \n•	Ảnh hưởng một số ngành nghề truyền thống\nỨng dụng AI trong giáo dục\nAI giúp:\n•	Hỗ trợ giải bài tập \n•	Cá nhân hóa lộ trình học \n•	Tạo quiz tự động \n•	Hỗ trợ AI Tutor trả lời học viên \n•	Phân tích tiến độ học tập\n•	Kết luận\n•	AI đang trở thành xu hướng công nghệ quan trọng của tương lai. Việc hiểu và sử dụng AI đúng cách sẽ giúp nâng cao hiệu quả học tập, làm việc và sáng tạo trong nhiều lĩnh vực khác nhau.\n\n',NULL,'Bài học giới thiệu khái niệm AI (Trí Tuệ Nhân Tạo), cách AI hoạt động, các loại AI phổ biến như Machine Learning và Deep Learning, cùng các ứng dụng thực tế trong đời sống và giáo dục. Học viên cần hiểu lợi ích, hạn chế và vai trò của AI trong thời đại công nghệ số.','',0,'',0,1,0,'2026-05-08 04:00:48','2026-05-10 06:57:42',NULL),(8,7,'Giới thiệu về AI','text','<h2 class=\"ql-align-center\"><strong>Giới thiệu về AI (Trí Tuệ Nhân Tạo)</strong></h2><p>\n<img src=\"/uploads/lessons/img_69fd6f1178779.jpg\">\nAI là gì?\nAI (Artificial Intelligence - Trí Tuệ Nhân Tạo) là công nghệ giúp máy tính có khả năng mô phỏng tư duy và hành vi của con người. AI có thể học từ dữ liệu, phân tích thông tin và đưa ra quyết định tự động.\nHiện nay AI được ứng dụng rất rộng rãi trong:\n•	Chatbot hỗ trợ khách hàng \n•	Nhận diện khuôn mặt \n•	Dịch ngôn ngữ \n•	Gợi ý video trên TikTok, YouTube \n•	Xe tự lái \n•	Hỗ trợ học tập và giáo dục \nCác loại AI phổ biến\n<strong><em>1. AI phản hồi tự động</em></strong>\nLà dạng AI có thể trả lời câu hỏi hoặc thực hiện tác vụ theo dữ liệu được huấn luyện.\nVí dụ:\n•	ChatGPT \n•	Siri \n•	Google Assistant \n<strong><em>2. Machine Learning</em></strong>\nMáy tính học từ dữ liệu để cải thiện kết quả theo thời gian mà không cần lập trình quá chi tiết.\nVí dụ:\n•	Gợi ý phim trên Netflix \n•	Đề xuất sản phẩm Shopee \n<strong><em>3. Deep Learning</em></strong>\nMột nhánh nâng cao của Machine Learning sử dụng mạng nơ-ron để xử lý dữ liệu phức tạp như hình ảnh, giọng nói và video.\nAI hoạt động như thế nào?\nAI thường hoạt động qua các bước:\n1.	Thu thập dữ liệu \n2.	Huấn luyện mô hình \n3.	Phân tích dữ liệu \n4.	Đưa ra kết quả hoặc dự đoán \nVí dụ:\n•	AI học hàng triệu hình ảnh mèo để nhận biết đâu là mèo.\nƯu điểm của AI\n•	Tự động hóa công việc \n•	Tiết kiệm thời gian \n•	Xử lý dữ liệu nhanh \n•	Hỗ trợ học tập và nghiên cứu \n•	Cá nhân hóa trải nghiệm người dùng\nHạn chế và rủi ro của AI\n•	Có thể đưa ra thông tin sai \n•	Phụ thuộc dữ liệu huấn luyện \n•	Nguy cơ lạm dụng Deepfake \n•	Ảnh hưởng một số ngành nghề truyền thống\nỨng dụng AI trong giáo dục\nAI giúp:\n•	Hỗ trợ giải bài tập \n•	Cá nhân hóa lộ trình học \n•	Tạo quiz tự động \n•	Hỗ trợ AI Tutor trả lời học viên \n•	Phân tích tiến độ học tập\n<strong>Kết luận</strong>\n•	AI đang trở thành xu hướng công nghệ quan trọng của tương lai. Việc hiểu và sử dụng AI đúng cách sẽ giúp nâng cao hiệu quả học tập, làm việc và sáng tạo trong nhiều lĩnh vực khác nhau.</p>','Hiểu về AI','Bài học giới thiệu tổng quan về AI (Trí Tuệ Nhân Tạo), khái niệm và cách AI mô phỏng tư duy con người thông qua học dữ liệu và phân tích thông tin. Nội dung bao gồm các loại AI phổ biến như AI phản hồi tự động, Machine Learning và Deep Learning; cách AI hoạt động qua các bước thu thập dữ liệu, huấn luyện mô hình và dự đoán kết quả. Ngoài ra bài học còn trình bày ưu điểm, hạn chế, rủi ro của AI và các ứng dụng thực tế trong giáo dục, học tập và đời sống hiện đại.','',0,'',0,1,0,'2026-05-08 04:31:45',NULL,NULL),(9,7,'Bài kiểm tra kiến thức','quiz','','','','',0,'',0,1,0,'2026-05-10 07:12:35',NULL,NULL),(10,8,'Xây dựng AI p1','video','<h1>XÂY DỰNG AI TỪ CƠ BẢN ĐẾN THỰC TẾ</h1><h2>1. AI là gì?</h2><p>Artificial Intelligence (AI) là trí tuệ nhân tạo giúp máy tính có khả năng học tập, suy luận và đưa ra quyết định giống con người.</p><h2>2. Các thành phần chính của AI</h2><p>AI gồm dữ liệu, thuật toán, mô hình học máy, hệ thống xử lý và triển khai thực tế.</p><h2>3. Machine Learning</h2><p>Machine Learning là nhánh của AI giúp máy học từ dữ liệu để dự đoán hoặc phân loại.</p><h2>4. Deep Learning</h2><p>Deep Learning sử dụng mạng nơ-ron nhiều tầng để xử lý dữ liệu phức tạp như hình ảnh, âm thanh và ngôn ngữ.</p><h2>5. Quy trình xây dựng AI</h2><p>Các bước gồm: xác định bài toán, thu thập dữ liệu, làm sạch dữ liệu, huấn luyện mô hình, đánh giá và triển khai.</p><h2>6. Kiến trúc hệ thống AI</h2><p>Một hệ thống AI thường gồm Frontend, Backend, AI Service và Database.</p><h2>7. AI trong LMS</h2><p>AI có thể tạo quiz, chatbot hỗ trợ học sinh, gợi ý khóa học và chấm điểm tự động.</p><h2>8. Công nghệ AI phổ biến</h2><p>Các công nghệ thường dùng: Python, TensorFlow, PyTorch, Scikit-learn, LangChain.</p><h2>9. Generative AI</h2><p>Generative AI có khả năng tạo văn bản, hình ảnh, video và mã nguồn.</p><h2>10. Hướng học AI</h2><p>Nên học Python, Machine Learning, Deep Learning và xây dựng dự án thực tế.</p><h2>Kết luận</h2><p>AI đang phát triển mạnh trong giáo dục, tài chính, y tế và công nghệ. Việc học và xây dựng AI sẽ mở ra nhiều cơ hội nghề nghiệp trong tương lai.</p>','Xây dựng AI','XÂY DỰNG AI TỪ CƠ BẢN ĐẾN THỰC TẾ\n1. AI là gì?\nArtificial Intelligence (AI) là trí tuệ nhân tạo giúp máy tính có khả năng học tập, suy luận và đưa ra quyết định giống con người.\n2. Các thành phần chính của AI\nAI gồm dữ liệu, thuật toán, mô hình học máy, hệ thống xử lý và triển khai thực tế.\n3. Machine Learning\nMachine Learning là nhánh của AI giúp máy học từ dữ liệu để dự đoán hoặc phân loại.\n4. Deep Learning\nDeep Learning sử dụng mạng nơ-ron nhiều tầng để xử lý dữ liệu phức tạp như hình ảnh, âm thanh và ngôn ngữ.\n5. Quy trình xây dựng AI\nCác bước gồm: xác định bài toán, thu thập dữ liệu, làm sạch dữ liệu, huấn luyện mô hình, đánh giá và triển khai.\n6. Kiến trúc hệ thống AI\nMột hệ thống AI thường gồm Frontend, Backend, AI Service và Database.\n7. AI trong LMS\nAI có thể tạo quiz, chatbot hỗ trợ học sinh, gợi ý khóa học và chấm điểm tự động.\n8. Công nghệ AI phổ biến\nCác công nghệ thường dùng: Python, TensorFlow, PyTorch, Scikit-learn, LangChain.\n9. Generative AI\nGenerative AI có khả năng tạo văn bản, hình ảnh, video và mã nguồn.\n10. Hướng học AI\nNên học Python, Machine Learning, Deep Learning và xây dựng dự án thực tế.\nKết luận\nAI đang phát triển mạnh trong giáo dục, tài chính, y tế và công nghệ. Việc học và xây dựng AI sẽ mở ra nhiều cơ hội nghề nghiệp trong tương lai.\n','',0,'7/8/19babf386837836e813d5a6bb435dd55.mp4',0,1,0,'2026-05-10 07:25:19',NULL,NULL);
/*!40000 ALTER TABLE `lessons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int DEFAULT NULL,
  `receiver_id` int DEFAULT NULL,
  `content` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sender` (`sender_id`),
  KEY `idx_receiver` (`receiver_id`),
  KEY `fk_messages_deleted_by` (`deleted_by`),
  KEY `idx_chat` (`sender_id`,`receiver_id`,`created_at`),
  KEY `idx_messages_sender` (`sender_id`),
  KEY `idx_messages_receiver` (`receiver_id`),
  CONSTRAINT `fk_messages_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (3,8,7,'Thầy ơi em không hiểu bài','2026-04-17 03:06:37',NULL,NULL),(4,7,8,'Em xem lại video nhé','2026-04-17 03:06:37',NULL,NULL),(5,17,7,'hello','2026-05-10 05:53:04',NULL,NULL),(6,17,7,'a','2026-05-10 05:58:48',NULL,NULL),(7,7,17,'a','2026-05-10 05:58:58',NULL,NULL),(8,17,7,'a','2026-05-10 06:00:07',NULL,NULL),(9,7,17,'a','2026-05-10 06:00:11',NULL,NULL),(10,7,17,'1','2026-05-10 06:04:36',NULL,NULL),(11,17,7,'chào giáo viên','2026-05-10 06:04:58',NULL,NULL),(12,17,7,'2','2026-05-10 08:48:47',NULL,NULL),(13,17,16,'aaaa','2026-05-10 08:49:12',NULL,NULL),(14,16,17,'123','2026-05-10 08:49:20',NULL,NULL);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (3,8,'Bạn đã đăng ký khóa học thành công',0,'2026-04-17 03:08:47'),(4,7,'Bạn có học viên mới',1,'2026-04-17 03:08:47');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quiz_id` int DEFAULT NULL,
  `question` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_quiz` (`quiz_id`),
  KEY `idx_quiz_id` (`quiz_id`),
  KEY `fk_questions_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_questions_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (1,1,'Java là ngôn ngữ gì?',NULL,NULL),(2,2,'SELECT dùng để làm gì?',NULL,NULL),(3,4,'Machine Learning là gì?',NULL,NULL),(4,4,'AI nào thường dùng để trả lời câu hỏi như ChatGPT?',NULL,NULL),(5,5,'Machine Learning là gì?',NULL,NULL),(6,5,'AI nào thường dùng để trả lời câu hỏi như ChatGPT?',NULL,NULL),(7,5,'AI là viết tắt của gì?',NULL,NULL);
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_results`
--

DROP TABLE IF EXISTS `quiz_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `quiz_id` int DEFAULT NULL,
  `score` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_quiz` (`user_id`,`quiz_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_quiz` (`quiz_id`),
  KEY `idx_quiz_results_user` (`user_id`),
  CONSTRAINT `quiz_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `quiz_results_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_results`
--

LOCK TABLES `quiz_results` WRITE;
/*!40000 ALTER TABLE `quiz_results` DISABLE KEYS */;
INSERT INTO `quiz_results` VALUES (3,8,1,8,'2026-04-17 03:06:24'),(4,9,1,10,'2026-04-17 03:06:24'),(5,17,4,100,'2026-05-10 08:46:37');
/*!40000 ALTER TABLE `quiz_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quizzes`
--

DROP TABLE IF EXISTS `quizzes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quizzes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lesson_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lesson` (`lesson_id`),
  KEY `fk_quizzes_deleted_by` (`deleted_by`),
  KEY `idx_lesson_deleted` (`lesson_id`,`deleted_at`),
  CONSTRAINT `fk_quizzes_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quizzes`
--

LOCK TABLES `quizzes` WRITE;
/*!40000 ALTER TABLE `quizzes` DISABLE KEYS */;
INSERT INTO `quizzes` VALUES (1,1,'Quiz Java cơ bản','2026-04-17 03:03:00',NULL,NULL),(2,3,'Quiz SQL','2026-04-17 03:03:00',NULL,NULL),(4,9,'Kiểm tra về hiểu biết AI','2026-05-10 07:12:35','2026-05-10 07:33:57',NULL),(5,9,'Kiểm tra về hiểu biết AI','2026-05-10 07:33:57',NULL,NULL);
/*!40000 ALTER TABLE `quizzes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `status` enum('pending','resolved') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_course` (`course_id`),
  KEY `user_id` (`user_id`),
  KEY `fk_reports_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_reports_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (2,8,1,'Nội dung không phù hợp','pending','2026-04-17 03:08:58',NULL,NULL);
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `teacher_code` varchar(20) DEFAULT NULL,
  `role` enum('student','teacher','admin') DEFAULT 'student',
  `is_vip` tinyint(1) DEFAULT '0',
  `status` enum('active','banned') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `uq_teacher_code` (`teacher_code`),
  KEY `idx_role` (`role`),
  KEY `idx_status` (`status`),
  KEY `deleted_by` (`deleted_by`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'hieule@gmail.com','hieule@gmail.com','$2y$10$iTPR5ofoVxurMbIr6vpE2eecTcpzt2P2e7WQKRvxhg2YdF08ueise',NULL,NULL,'student',0,'active','2026-04-15 17:57:54',NULL,'2026-04-23 16:45:52',NULL,'2026-04-16 14:45:58'),(2,'hieule','hieu@gmail.com','$2y$10$iTPR5ofoVxurMbIr6vpE2eecTcpzt2P2e7WQKRvxhg2YdF08ueise',NULL,NULL,'student',0,'active','2026-04-16 14:39:46',NULL,NULL,NULL,'2026-04-16 14:46:16'),(4,'hieu','abc@gmail.com','$2y$10$iTPR5ofoVxurMbIr6vpE2eecTcpzt2P2e7WQKRvxhg2YdF08ueise',NULL,NULL,'student',0,'active','2026-04-16 14:46:55',NULL,NULL,NULL,'2026-04-16 15:26:49'),(5,'abc','abcd@gmail.com','$2y$10$iTPR5ofoVxurMbIr6vpE2eecTcpzt2P2e7WQKRvxhg2YdF08ueise',NULL,NULL,'student',0,'active','2026-04-16 14:49:13',NULL,NULL,NULL,'2026-04-16 15:14:27'),(6,'admin','admin@gmail.com','$2y$10$iTPR5ofoVxurMbIr6vpE2eecTcpzt2P2e7WQKRvxhg2YdF08ueise',NULL,NULL,'admin',1,'active','2026-04-16 15:21:57',NULL,NULL,NULL,'2026-05-10 06:54:18'),(7,'teacher1','teacher@gmail.com','$2y$10$iTPR5ofoVxurMbIr6vpE2eecTcpzt2P2e7WQKRvxhg2YdF08ueise','/uploads/avatars/avatar_7_1776965204.png',NULL,'teacher',1,'active','2026-04-16 15:21:57',NULL,NULL,NULL,'2026-05-10 05:43:37'),(8,'studenttt','student@gmail.com','$2y$10$iTPR5ofoVxurMbIr6vpE2eecTcpzt2P2e7WQKRvxhg2YdF08ueise','/uploads/avatars/avatar_8_1776966006.png',NULL,'student',1,'active','2026-04-16 15:21:57',NULL,NULL,NULL,'2026-05-08 05:06:03'),(9,'student2','student2@gmail.com','$2y$10$iTPR5ofoVxurMbIr6vpE2eecTcpzt2P2e7WQKRvxhg2YdF08ueise',NULL,NULL,'student',0,'active','2026-04-16 15:21:57',NULL,NULL,NULL,NULL),(10,'test01','test@gmail.com','$2y$10$UnTyaTd3fLwNWxJ4PH14/O0Ph3Y4MJlb3Vul8p7prbXX9.996qtR6',NULL,NULL,'student',0,'active','2026-04-24 13:39:20',NULL,NULL,NULL,'2026-04-24 13:39:20'),(11,'hieu le','a111@gmail.com','$2y$10$jogs8iZQr6i.PSVGl5yHp.21Iz0Qw6bws6aaqkW9w10sRbqW25wPu','/uploads/avatars/avatar_11_1777094568.png',NULL,'student',0,'active','2026-04-25 03:06:09',NULL,NULL,NULL,'2026-05-08 05:05:54'),(12,'a1223','a1223@gmail.com','$2y$10$wWmTHNIxzXOmVqzmgiwAieL/etJsMzA2LjrJ.R.SLaNiSpHuDK/ge',NULL,NULL,'student',0,'active','2026-04-25 04:10:08',NULL,NULL,NULL,'2026-04-25 04:10:08'),(16,'teacher123','teacher1@gmail.com','$2y$10$eWJHAF59hhtkzzhVeJrWpeZC25Naj2KFNn/43ovkmzQcmJIoXZD1e',NULL,NULL,'teacher',0,'active','2026-05-08 03:39:58',NULL,NULL,NULL,'2026-05-10 09:01:04'),(17,'a123@gmail.com','a123@gmail.com','$2y$10$DLQO13IolzUzU6YZd/mDkeMgs0M7w2vQI0j0yhPhdCQ4HtbisHnbu','/uploads/avatars/avatar_17_1778394281.png',NULL,'student',0,'active','2026-05-09 06:31:16',NULL,NULL,NULL,'2026-05-10 09:00:49');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `video_tokens`
--

DROP TABLE IF EXISTS `video_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `video_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `lesson_id` int NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_token` (`token`),
  KEY `idx_expires` (`expires_at`),
  KEY `idx_user_lesson` (`user_id`,`lesson_id`),
  KEY `lesson_id` (`lesson_id`),
  CONSTRAINT `video_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `video_tokens_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `video_tokens`
--

LOCK TABLES `video_tokens` WRITE;
/*!40000 ALTER TABLE `video_tokens` DISABLE KEYS */;
INSERT INTO `video_tokens` VALUES (33,'76cb9fb95abe5108281b65b62173384118760062e39486ec52e425b1d6914de5',6,10,'2026-05-10 11:25:28','2026-05-10 07:25:28'),(35,'c6f3dcef062cc68219faf3b12c0b3d6898b7c900da49ed549e28c9bb10e0d5cf',17,4,'2026-05-10 12:38:16','2026-05-10 08:38:16'),(38,'82d97fe4aceee0d66b8f2ab4d6c69c05d9587bd592b43ba59631674dbdddb808',17,10,'2026-05-10 13:06:14','2026-05-10 09:06:14');
/*!40000 ALTER TABLE `video_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vip_payments`
--

DROP TABLE IF EXISTS `vip_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vip_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `vip_payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vip_payments`
--

LOCK TABLES `vip_payments` WRITE;
/*!40000 ALTER TABLE `vip_payments` DISABLE KEYS */;
INSERT INTO `vip_payments` VALUES (2,8,100000.00,'completed','2026-04-17 03:08:36'),(3,8,50000.00,'completed','2026-04-17 03:29:30'),(4,8,50000.00,'completed','2026-04-17 03:56:03');
/*!40000 ALTER TABLE `vip_payments` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-10 16:45:59
