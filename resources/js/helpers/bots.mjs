import axios from 'axios';
//import CryptoJS from "crypto-js" 


export const getFullName = (firstname, lastName) => {
    return `my fullname is ${firstname} ${lastName}`;
};

export const balances = (_bot) => {
    return `my botName is ${_bot} `;
};

export const getBalances = (_bot) => {

console.log(_bot);

 const event = new Date();
    const timestamp = event.toISOString();
    return new Promise((resolve, reject) => {
        var querystring = "timestamp=" + timestamp;

  //      const getSignature = CryptoJS.HmacSHA256(querystring,_bot.apiKey);
  //          console.log(getSignature.words)
            //var hash = CryptoJS.HmacSHA256("Message", "Secret Passphrase");
            // .update(querystring)
            // .digest('hex');
        // make the actual GET request
        var config = {
          method: 'get',
          url: `https://signal.revenyou.io/${_bot.scope}/api/bnf/balances`,
          params: {
            timestamp: timestamp
          },
          headers:{
            "X-BOT": _bot.name,
            "Sign" : 'getSignature'
          }
        };
        axios(config)
            .then(function (response) {
            resolve(JSON.stringify(response.data));
            resolve(response);
          })

        .catch(function(err) {
            console.log(err)
              resolve(err.message);
        });   
    });


};

