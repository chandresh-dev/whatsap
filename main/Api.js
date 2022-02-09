const { loadSessionOrGenerateQR } = require("./Connection.js");
const { validationResult, query } = require("express-validator");
const { MessageType } = require("@adiwajshing/baileys");
const fs = require("fs");
const { default: axios } = require("axios");
const { loadSessionOrGenereateQR } = require("./Connection.js");
const { db } = require("./Database.js");

const sendMessage = async (req, res) => {

    const errors = validationResult(req);
    if (!errors.isEmpty()) return res.status(400).json({ status: false, msg: errors.array()[0].msg })
    const number = phoneNumberFormatter(req.body.number)
    if (!fs.existsSync(`./main/whatsapp-session-${req.body.sender}.json`)) return res.status(400).json({ status: false, msg: "Please scan the QR code before using API 2" });
    const client = await loadSessionOrGenerateQR(req.body.sender);
    const numberExists = await client.isOnWhatsApp(number);
    if (!number.endsWith('@g.us')) {
        if (numberExists == undefined) return res.status(400).json({ status: false, msg: "Destination number does not have whatsapp" });
    }

    if (client.state != 'open') return res.status(400).json({ status: false, msg: "Please scan the QR code before using API 2" });

    // await client.sendMessage(number, req.body.message, MessageType.text).then((result) => {
    //     return res.status(200).json({
    //         status: true, data: {
    //             number: req.body.number,
    //             message: req.body.message,
    //             timeStamps: result.messageTimestamp
    //         }
    //     });
    // }).catch((err) => {
    //     return res.status(400).json({ status: false, msg: err })
    // })
    client.sendMessage(number, req.body.message, MessageType.text)
    return res.status(200).json({
        status: true, data: {
            number: req.body.number,
            message: req.body.message,

        }
    });

}


const sendImage = async (req, res) => {
    const number = phoneNumberFormatter(req.body.number)

    const errors = validationResult(req);
    if (!errors.isEmpty()) return res.status(400).json({ status: false, data: errors.array()[0].msg })
    if (!fs.existsSync(`./main/whatsapp-session-${req.body.sender}.json`)) return res.status(400).json({ status: false, data: { msg: "Please scan the QR code before using API 2" } });
    const client = await loadSessionOrGenerateQR(req.body.sender);

    const numberExists = await client.isOnWhatsApp(number);
    if (!number.endsWith('@g.us')) {
        if (numberExists == undefined) return res.status(400).json({ status: false, data: { msg: "Destination number does not have whatsapp" } });
    }

    if (client.state != 'open') return res.status(400).json({ status: false, data: { msg: "Please scan the QR code before using the API" } });

    client.sendMessage(number, { url: req.body.url }, MessageType.image, {
        caption: req.body.message
    })
    return res.status(200).json({
        status: true, data: {
            number: req.body.number,
            message: req.body.message,
            url: req.body.url,

        }
    });

}
const sendDocument = async (req, res) => {
    const number = phoneNumberFormatter(req.body.number)

    const errors = validationResult(req);
    if (!errors.isEmpty()) return res.status(400).json({ status: false, data: errors.array()[0].msg })
    if (!fs.existsSync(`./main/whatsapp-session-${req.body.sender}.json`)) return res.status(400).json({ status: false, data: { msg: "Please scan the QR code before using API 2" } });
    const client = await loadSessionOrGenerateQR(req.body.sender);

    const numberExists = await client.isOnWhatsApp(number);
    if (!number.endsWith('@g.us')) {
        if (numberExists == undefined) return res.status(400).json({ status: false, data: { msg: "Destination number does not have whatsapp" } });
    }

    if (client.state != 'open') return res.status(400).json({ status: false, data: { msg: "Please scan the QR code before using API 2" } });
    // get name and tipe file
    const url = req.body.url;
    const arrayUrl = url.split("/");
    const explode = arrayUrl[arrayUrl.length - 1];
    const d = explode.split(".");

    const filetype = d[d.length - 1];
    // mimetype :  d[d.length - 1]
    const response = await axios.get(url, { responseType: 'arraybuffer' });
    const buffer = Buffer.from(response.data, "utf-8");

    client.sendMessage(number, buffer, MessageType.document, {
        filename: explode,
        mimetype: filetype
    })
    return res.status(200).json({
        status: true, data: {
            number: req.body.number,
            url: req.body.url,

        }
    });

}


const sendButton = async (req, res) => {
    const number = phoneNumberFormatter(req.body.number)
    const errors = validationResult(req);
    if (!errors.isEmpty()) return res.status(400).json({ status: false, data: errors.array()[0].msg })
    if (!fs.existsSync(`./main/whatsapp-session-${req.body.sender}.json`)) return res.status(400).json({ status: false, data: { msg: "Please scan the QR code before using API" } });
    const client = await loadSessionOrGenerateQR(req.body.sender);
    const numberExists = await client.isOnWhatsApp(number);
    if (!number.endsWith('@g.us')) {
        if (numberExists == undefined) return res.status(400).json({ status: false, data: { msg: "Destination number does not have whatsapp" } });
    }
    if (client.state != 'open') return res.status(400).json({ status: false, data: { msg: "Please scan the QR code before using API 2" } });
    const buttons = [
        { buttonId: 'id1', buttonText: { displayText: req.body.button1 }, type: 1 },
        { buttonId: 'id2', buttonText: { displayText: req.body.button2 }, type: 1 }
    ]
    const buttonMessage = {
        contentText: req.body.message,
        footerText: req.body.footer,
        buttons: buttons,
        headerType: 1
    }
    client.sendMessage(number, buttonMessage, MessageType.buttonsMessage)
    return res.status(200).json({
        status: true, data: {
            number: req.body.number,
            message: req.body.message,

        }
    });

}




const sendBlast = async (req, res) => {
    const list = req.body.lists;
    const errors = validationResult(req);
    if (!errors.isEmpty()) return res.status(400).json({ status: false, data: errors.array()[0].msg })

    if (!fs.existsSync(`./main/whatsapp-session-${req.body.sender}.json`)) return res.status(400).json({ status: false, msg: "Please scan the QR code before using API 2" });
    const client = await loadSessionOrGenerateQR(req.body.sender);
    //  const numberExists = await client.isOnWhatsApp(number);

    if (client.state != 'open') return res.status(400).json({ status: false, msg: "Please scan the QR code before using API 2" });
    let n = 0;
    var i = 0;

    var arrayTotal = list.length;
    async function f() {
        const no = list[i];
        const number = phoneNumberFormatter(list[i])
        await client.sendMessage(number, req.body.message, MessageType.text).then(res => {
            db.query(`INSERT INTO blast (sender,nomor,message,make_by,status) VALUES ('${req.body.sender}','${no}','${req.body.message}','${req.body.makeby}','Success')`, err => {
                console.log(err)
            })
            console.log(`Successfully send message to number ${number}`);
        }).catch(err => {
            db.query(`INSERT INTO blast (sender,nomor,message,make_by,status) VALUES ('${req.body.sender}','${no}','${req.body.message}','${req.body.makeby}','Failed')`, err => {
                console.log(err)
            })
            console.log(`Failed to send message => ${number}`);
        })
        i++;
        if (i < arrayTotal) {
            setTimeout(f, req.body.delay * 1000);
        }
    }

    f();
    return res.status(200).json({ status: true, msg: 'Sending a message is in progress, the status of the message can be seen in the table below this page' });

    //  return res.status(503).json({ status: true, msg: 'Gagal kirim' });
}

const sendBlastMedia = async (req, res) => {
    const list = req.body.lists;

    const errors = validationResult(req);
    if (!errors.isEmpty()) return res.status(400).json({ status: false, msg: errors.array()[0].msg })
    if (!fs.existsSync(`./main/whatsapp-session-${req.body.sender}.json`)) return res.status(400).json({ status: false, msg: "Please scan the QR code before using API 2" });
    const client = await loadSessionOrGenerateQR(req.body.sender);


    if (client.state != 'open') return res.status(400).json({ status: false, msg: "Please scan the QR code before using API 2" });
    var i = 0;

    var arrayTotal = list.length;
    async function f() {
        const no = list[i];
        const number = phoneNumberFormatter(list[i])
        await client.sendMessage(number, { url: req.body.media }, MessageType.image, {
            caption: req.body.message
        }).then(res => {
            db.query(`INSERT INTO blast (sender,nomor,message,media,make_by,status) VALUES ('${req.body.sender}','${no}','${req.body.message}','${req.body.media}','${req.body.makeby}','Success')`, err => {
                console.log(err)
            })
            console.log(`Successfully send message to number ${number}`);
        }).catch(err => {
            db.query(`INSERT INTO blast (sender,nomor,message,media,make_by,status) VALUES ('${req.body.sender}','${no}','${req.body.message}','${req.body.media}','${req.body.makeby}','Failed')`, err => {
                console.log(err)
            })
            console.log(`Failed to send message => ${number}`);
        })
        i++;
        if (i < arrayTotal) {
            setTimeout(f, req.body.delay * 1000);
        }
    }

    f();
    return res.status(200).json({ status: true, msg: 'Sending a message is in progress, the status of the message can be seen in the table below this page' });


}






function dbQuery(query) {
    return new Promise(data => {
        db.query(query, (err, res) => {
            if (err) throw err;
            try {
                data(res);
            } catch (error) {
                data({});
                throw error;
            }
        })
    })
}

const validateKey = async (key = 'sdfsd', number = '082223423') => {

    const data = await dbQuery(`SELECT * FROM device JOIN account ON account.username = device.pemilik WHERE nomor = ${number}`);
    if (data.length === 0) return false;
    if (data[0].api_key != key) return false;
    return true;
}



try {
    setInterval((async d => {
        const k = await dbQuery("SELECT * FROM device WHERE connect = '1' ").then(res => {
            res.forEach(data => {
                console.log(data)
                const client = loadSessionOrGenerateQR(data.nomor);
            })
        })

    }), 600_000)

} catch (e) {
    logger.debug(e)
}

const phoneNumberFormatter = function (number) {
    // 1. Menghilangkan karakter selain angka
    if (number.endsWith('@g.us')) {
        return number
    }
    let formatted = number.replace(/\D/g, '');

    // 2. Menghilangkan angka 0 di depan (prefix)
    //    Kemudian diganti dengan 62
    if (formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1);
    }

    if (!formatted.endsWith('@c.us')) {
        formatted += '@c.us';
    }

    return formatted;
}

module.exports = {
    sendMessage, sendImage, sendDocument, validateKey, sendButton, sendBlast, sendBlastMedia, phoneNumberFormatter
}
// https://m-pedia.id
// contact 6282298859671 / ilmansunannudin2@gmail.com if you need any help. 
