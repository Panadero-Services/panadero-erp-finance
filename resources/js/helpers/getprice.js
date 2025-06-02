"use strict"; 
// * * *     *       *   *       *   *   *   * *** *
// *    *       *     *      *   *       *   *     *
// *   SCRIPT : getprice.js      * *         * 
// *   Location laravel10/getprice.js            * * 
// *   Modified :JaWsome.Orbit   *                 * 
// *   Date:    21 jun 2023             *          *
// *   Version: v1.0.1.            *        *      *
// ** *     *       *   *       *   *   *   *     **
// * *  *       *     *      *   *       *  *  * * *

/**
 * general sql routines 
 * 
 *  
 */

  // dotenv
  const dotenv = require('dotenv');
  dotenv.config();

  // mysql
  const Mysql = require('mysql');
//  let mysql = Mysql.createPool({
  let mysql = Mysql.createConnection({
    connectionLimit : 10,
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE
  });

  const Moralis = require("moralis").default;
  const { EvmChain } = require("@moralisweb3/common-evm-utils");

  const runApp = async () => {
    await Moralis.start({
      apiKey: process.env.MORALIS_KEY
      // ...and any other configuration
    });

    const address = "0x50c34995a273075a80c23625F69F40d56CE414DE";

    const chain = EvmChain.BSC;

    const response = await Moralis.EvmApi.token.getTokenPrice({
      address,
      chain,
    });

    return (response.toJSON());

  };

  // called by strategy_pong.js
  const runMysql = async (_token, _price) => {
    return new Promise((resolve, reject) => {
      try {
        const q = `update prices SET price = ${_price} where token = '${_token}'`;
        console.log(q);
       	mysql.query(q,function(err, r, fields) {
          console.log(r);
          resolve(r);
        });
      } catch (err) {
        console.log(err);
        err.msg = `SMTH WRONG WITH runMysql()`;
        reject(err);
      }
    });
  }

const _start = async () => {

  let price = await runApp();
  console.log(price.tokenSymbol, price.usdPrice);

  let getRec = await runMysql(price.tokenSymbol, price.usdPrice);
  console.log(getRec)
  mysql.end();


};



_start();



