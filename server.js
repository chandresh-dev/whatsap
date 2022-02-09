const { MessageType } = require("@adiwajshing/baileys");
const http = require("http");
const express = require("express");
const { body } = require("express-validator");
const app = express();
const server = http.createServer(app);
const fs = require("fs");
const { sendMessage, sendImage, sendDocument, validateKey, sendButton, sendBlast, sendBlastMedia, phoneNumberFormatter } = require("./main/Api.js");
const router = express.Router();
const { loadSessionOrGenerateQR } = require('./main/Connection.js')
const { Server } = require("socket.io");
const io = new Server(server);
const { db } = require("./main/Database.js");
var CronJob = require('cron').CronJob;
require('dotenv').config();
const port = process.env.PORT || 7000;
app.use(express.json());
app.use(express.urlencoded({ extended: true }))
app.use(router);


// router  API
router.post('/send-message', [
    body('sender', 'Parameter Salah').notEmpty(),
    body('number', "Parameter salah").notEmpty(),
    body('message', 'Parameter Salah').notEmpty(),
    body('api_key', 'Parameter Salah').notEmpty(),
    body('api_key').custom((value, { req }) => {

        const result = validateKey(value, req.body.sender);
        if (result === false) throw new Error('API key salah!');
        return true;

    })
], sendMessage)

router.post('/send-message', [

    body('sender', 'Parameter Salah').notEmpty(),
    body('number', "Parameter salah").notEmpty(),
    body('message', 'Parameter Salah').notEmpty(),
    body('api_key', 'Parameter Salah').notEmpty(),
    body('api_key').custom((value, { req }) => {

        const result = validateKey(value, req.body.sender);
        if (result === false) throw new Error({ msg: 'Api key salah' });
        return true;

    })
], sendMessage)


router.post('/send-image', [
    body('sender', 'Parameter Salah').notEmpty(),
    body('api_key', "Parameter salah").notEmpty(),
    body('number', "Parameter salah").notEmpty(),
    body('message', 'Parameter Salah').notEmpty(),
    body('url', 'Parameter Salah').notEmpty(),
    body('api_key').custom(async (value, { req }) => {

        const result = await validateKey(value, req.body.sender);
        if (result === false) throw new Error('Api key salah');
        return true;

    })
], sendImage)
router.post('/send-document', [
    body('api_key', "Parameter salah").notEmpty(),
    body('sender', 'Parameter Salah').exists(),
    body('number', "Parameter salah").notEmpty(),
    body('url', 'Parameter Salah').notEmpty(),
    body('api_key').custom(async (value, { req }) => {

        const result = await validateKey(value, req.body.sender);
        if (result === false) throw new Error('Api key salah');
        return true;

    })
], sendDocument)
router.post('/send-button', [
    body('number', "Parameter salah").notEmpty(),
    body('api_key', "Parameter salah").notEmpty(),
    body('sender', 'Parameter Salah').notEmpty(),
    body('message', 'Parameter Salah').notEmpty(),
    body('button1', "Parameter salah").notEmpty(),
    body('button2', 'Parameter Salah').notEmpty(),
    body('footer', 'Parameter salah!'),
    body('api_key').custom(async (value, { req }) => {

        const result = await validateKey(value, req.body.sender);
        if (result === false) throw new Error('Api key salah');
        return true;

    })
], sendButton)

router.post('/send-blast', sendBlast)
router.post('/send-blast-media', sendBlastMedia)



//socket



io.on('connect', (Socket) => {

    Socket.on('startconnection', (id) => {
        loadSessionOrGenerateQR(id, Socket);
    })

    Socket.on('logoutdevice', async id => {

        db.query(`UPDATE device SET connect = '0' WHERE nomor = '${id}'`, (err, res) => {
            if (err) throw err;
            Socket.emit('disconnect2')
        });
        const client = await loadSessionOrGenerateQR(id, Socket, true)

        if (client == 'notlogin') {
            if (fs.existsSync(`./main/whatsapp-session-${id}.json`)) {
                fs.unlinkSync(`./main/whatsapp-session-${id}.json`);
            }
            Socket.emit('successLogout', id);
        } else
            if (client.state == 'open') {
                if (fs.existsSync(`./main/whatsapp-session-${id}.json`)) {
                    fs.unlinkSync(`./main/whatsapp-session-${id}.json`);
                }

                client.logout();
                client.clearAuthInfo();

                client.close()

            }
    })
})



// this is cronjob for schedule msg
function addZero(str) {
    return str < 10 ? ('0' + str) : str;
}
var job = new CronJob('* * * * * *', async () => {
    var currentdate = new Date();
    var datetime = addZero(currentdate.getFullYear()) + "-" +
        addZero(currentdate.getMonth() + 1) + "-" +
        addZero(currentdate.getDate()) + " " +
        addZero(currentdate.getHours()) + ":" +
        addZero(currentdate.getMinutes()) + ":" +
        addZero(currentdate.getSeconds());
    // get all  sender from schedule
    let sender = await new Promise((resolve, reject) => db.query("SELECT DISTINCT sender FROM schedule WHERE status = 'Pending'", (err, results) => {
        if (err) {
            reject(err)
        } else {
            resolve(results);
        }
    }));
    //
    // looping sender
    for (let i = 0; i < sender.length; i++) {
        const num = sender[i].sender;
        // check status sender
        const status = await new Promise((resolve, reject) => db.query(`SELECT connect FROM device WHERE nomor = '${num}'`, (err, res) => {
            if (err) {
                reject(err)
            } else {
                resolve(res)
            }
        }))
        if (status[0].connect === 1) {

            const dataSchedule = await new Promise((resolve, reject) => db.query(`SELECT * FROM schedule WHERE sender = '${num}' AND status = 'Pending'  AND jadwal < '${datetime}' LIMIT 100`, (err, res) => {
                if (err) {
                    reject(err)
                } else {
                    resolve(res)
                }
            }))
            if (dataSchedule.length > 0) {
                const client = await loadSessionOrGenerateQR(num);
                if (client.state == 'open') {
                    for (let i = 0; i < dataSchedule.length; i++) {
                        const receipt = dataSchedule[i].nomor
                        const msg = dataSchedule[i].pesan;
                        // const sch = dataSchedule[i].jadwal;
                        const media = dataSchedule[i].media;
                        const id = dataSchedule[i].id;

                        const number = phoneNumberFormatter(receipt)
                        var numberExists;
                        if (!number.endsWith('@g.us')) {
                            numberExists = true;
                        } else {
                            numberExists = await client.isOnWhatsApp(number);
                        }
                        if (numberExists) {
                            client.sendMessage(number, msg, MessageType.text).then(res => {
                                db.query(`UPDATE schedule SET status = 'Success' WHERE id =${id}`)
                            }).catch(err => {
                                db.query(`UPDATE schedule SET status = 'Failed' WHERE id =${id}`)
                            })
                        }

                    }
                }
            }
        }
    }
}, null, true, 'Asia/Jakarta');
job.start();

server.listen(process.env.PORT, () => {
    console.log('Server running at port ' + process.env.PORT)
})

//Asia/Jakarta