# aus_scraper

下記 URL をスクレイピングして CSV に出力するスクリプトです。
- https://www.englishaustralia.com.au/college_courses.php?id=123
- http://www.neas.org.au/studentsagents/centre-locator/?country=AU&name_search=&num_per_page=9999

CSV には下記の情報を出力します。
- 学校名
- 地域名(州の1つ下のレベル)
- 住所

スクレイピングには Goutte を使用しました。  
PHP の SimpleXMLElement で扱いづらい、文法エラーのある XML も処理できたためです。
