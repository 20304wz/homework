const express = require('express');
const mysql = require('mysql');
const cors = require('cors');

const app = express();
const port = 3000;

// 使用CORS中间件
app.use(cors());

// 创建MySQL连接
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root', // 替换为你的MySQL用户名
  password: '20030304Yjm.', // 替换为你的MySQL密码
  database: 'questionnaire' // 替换为你的数据库名
});

// 连接到数据库
db.connect((err) => {
  if (err) throw err;
  console.log('MySQL连接成功');
});

// 创建API接口
app.get('/data', (req, res) => {
  const query = 'SELECT * FROM tableclass'; //
  db.query(query, (err, results) => {
    if (err) throw err;
    res.json(results);
  });
});

// 启动服务器
app.listen(port, () => {
  console.log(`服务器运行在 http://localhost:${port}`);
});
