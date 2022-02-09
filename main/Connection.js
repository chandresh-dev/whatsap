// const db = require("./Database.js");
const { WAConnection, ReconnectMode, MessageType, } = require("@adiwajshing/baileys");
const fs = require("fs");

const { db } = require("./Database.js");
const axios = require("axios");
const sessionPath = "./main/sessions.json";
const qrcode = require("qrcode");
const { connect } = require("http2");
if (!fs.existsSync(sessionPath)) {
    fs.writeFileSync(sessionPath, JSON.stringify([]));
}


// https://m-pedia.id
// contact 6282298859671 / ilmansunannudin2@gmail.com if you need any help. 
const loadSessionOrGenerateQR = async (id, socket = null, logout = false) => {
    const client = new WAConnection();
    client.browserDescription = ["WAMP", "Chrome"];
    client.autoReconnect = ReconnectMode.onConnectionLost;
    client.setMaxListeners(0)

    client.connectOptions = {
        maxIdleTimeMs: 10_000,
        phoneResponseTime: 10_000,
        alwaysUseTakeover: false,

    }

    client.connectOptions.maxRetries = Infinity;

    // get event from baileys
    client.on('open', async () => {
        const numberrr = phoneNumberFormatter(client.user.jid);
        const mynumberrr = numberrr.replace(/\D/g, '');
        if (socket != null) {
            socket.emit('connected', {
                nomor: mynumberrr,
                nama: client.user.name,
                phone: client.user.phone
            })
        }
    })
    // socket handling
    const ID_SESSION_PATH = `./main/whatsapp-session-${id}.json`;
    client.on('chat-update', async result => {


        if (result.messages && result.count) {
            const m = result.messages.all()[0] // pull the new message from the update

            let sender = m.key.remoteJid
            const messageContent = m.message
            const messageType = Object.keys(messageContent)[0]
            console.log(messageType)
            if (messageType == 'conversation') {
                var text = m.message.conversation
            } else if (messageType == 'extendedTextMessage') {
                var text = m.message.extendedTextMessage.text
            } else if (messageType == 'imageMessage') {
                var text = m.message.imageMessage.caption
            } else if (messageType == 'buttonsResponseMessage') {
                var text = m.message.buttonsResponseMessage.selectedDisplayText;
            }
            const number = phoneNumberFormatter(client.user.jid);
            const mynumber = number.replace(/\D/g, '');
            db.query(`SELECT * FROM autoreply WHERE keyword = "${text}" AND nomor = "${mynumber}"`, async (err, results) => {
                if (err) throw err;

                if (results.length === 0) return;
                if (!results[0].media) {
                    client.sendMessage(sender, results[0].response, MessageType.text);
                } else {
                    const url = results[0].media;
                    const file = url.split("/");
                    const namefile = file[file.length - 1];
                    const explodename = namefile.split(".");
                    const typefile = explodename[1];

                    switch (typefile) {
                        case 'jpg':
                        case 'png':
                        case 'jpeg':
                            client.sendMessage(sender, { url: url }, MessageType.image, { caption: results[0].response }).catch(err => {
                                console.log('Failed to reply message')
                            })
                            break;
                        case 'pdf':
                        case 'doc':
                        case 'docx':
                            try {
                                //console.log(url)
                                const response = await axios.get(url, { responseType: 'arraybuffer' });
                                const buffer = Buffer.from(response.data, "utf-8");
                                client.sendMessage(sender, buffer, MessageType.document, {
                                    filename: namefile,
                                    mimetype: typefile
                                })
                            } catch (err) {
                                console.log("Failed" + err)
                            }
                    }
                }
            })
            // webhook
            db.query(`SELECT link_webhook FROM device WHERE  nomor = "${mynumber}"`, async (err, res) => {
                if (err) throw err;
                if (res.length === 0) return;
                const webhooklink = res[0].link_webhook;
                const respond = await sendHook(webhooklink, { sender: sender, message: text });
                //  console.log(respond)
                switch (respond.mode) {
                    case 'chat':
                        await client.sendMessage(sender, respond.pesan, MessageType.text);
                        break;
                    case 'reply':
                        await client.sendMessage(sender, respond.pesan, MessageType.extendedText, { quoted: m });
                    case 'picture':
                        await client.sendMessage(sender, { url: respond.data.url }, MessageType.image, { caption: respond.data.caption });
                    default:
                        return;
                }
            })
        }

    })

    client.on('open', async () => {

        const newsession = client.base64EncodedAuthInfo();

        fs.writeFileSync(ID_SESSION_PATH, JSON.stringify(newsession));
        db.query(`UPDATE device SET connect = '1' WHERE nomor = '${id}'`, (err, res) => {
            if (err) throw err;
        });
    })

    client.on('connection-phone-change', async (res) => {
        console.log(res)
    })

    client.on('initial-data-received', async () => {
        // console.log(client.contacts);
        fs.writeFileSync(`./main/contacts/${id}.json`, JSON.stringify(client.contacts));
    })
    client.on("close", async ({ reason, isReconnecting }) => {
        console.log("Disconnected because " + reason + ", reconnecting: " + isReconnecting)
        db.query(`UPDATE device SET connect = '0' WHERE nomor = '${id}'`, (err, res) => {
            if (err) throw err;
        });
        if (socket !== null) {
            socket.emit('disconnect2');

        }
        if (!isReconnecting && reason == "invalid_session") {
            if (fs.existsSync(`./main/whatsapp-session-${id}.json`)) {
                fs.unlinkSync(`./main/whatsapp-session-${id}.json`);
            }
        }
        client.clearAuthInfo();

    });

    // end get event



    if (fs.existsSync(ID_SESSION_PATH) && client.canLogin() === false) {


        client.loadAuthInfo(ID_SESSION_PATH);
        await client.connect().then(async (res) => {
            const numberr = phoneNumberFormatter(client.user.jid);
            const mynumberr = numberr.replace(/\D/g, '');
            if (socket != null) {
                const newsession = client.base64EncodedAuthInfo();

                fs.writeFileSync(ID_SESSION_PATH, JSON.stringify(newsession));
                db.query(`UPDATE device SET connect = '1' WHERE nomor = '${id}'`, (err, res) => {
                    if (err) throw err;
                });
                const d = await client.getProfilePicture()
                socket.emit('connected', {
                    nomor: mynumberr,
                    nama: client.user.name,
                    phone: client.user.phone,
                    img: d
                })
            }

        }).catch((err) => {
            console.log("Error, please re-scan!" + err)
        })

        //socket handling 



        return client;
    } else {

        let qrRetry = 0;

        // handle if this function called only for logout
        if (logout != false) {
            return 'notlogin';
        }
        //
        client.on('qr', (qr) => {
            console.log(qr);
            if (socket != null) {
                qrcode.toDataURL(qr, (err, url) => {
                    if (err) throw err;
                    socket.emit('qrgenerated', { url: url, notice: 'QR Code generated, Please scan' });
                })
            }
            // client.removeAllListeners('qr')
            //  client.close()

            if (qrRetry >= 2) {
                // close function is called before a new initTimeout is created, NodeJS will attempt to clear the old timeout.

                client.close();

            }
            qrRetry++;
            console.log(qrRetry)

        })
        await client.connect().then(async () => {

        }).catch((err) => {
            // socket.emit('timedout');

            console.log('NOT scanned, error' + err)
            return;
        })



    }

}



async function sendHook(url, data) {
    var result = {
        mode: 'There is no webhook for that keyword'
    };
    await axios.post(url, data).then(res => {
        if (res.data != null) result = res.data
    }).catch(err => {
        console.log(err)
    })

    return result;

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

// connect("6282298859671");
module.exports = { loadSessionOrGenerateQR }



