const mysql = require("mysql2");
require('dotenv').config();
const db = mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASS,
    database: process.env.DB_NAME
})

db.connect((err) => {
    if (err) throw err;
    console.log('database Connected.')
})




module.exports = { db }
// https://m-pedia.id
// contact 6282298859671 / ilmansunannudin2@gmail.com if you need any help. 