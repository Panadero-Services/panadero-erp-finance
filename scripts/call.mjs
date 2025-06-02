"use strict"; 
// * * *     *       *   *       *   *   *   * *** *
// *    *       *     *      *   *       *   *     *
// *   SCRIPT : call.js       * *         * 
// *   Description : test script for interacting bots api    * *         * 
// *   Location ... /call.js            * * 
// *   Modified :JaWsome.Orbit   *                 * 
// *   Date:    11 sep 2023             *          *
// *   Version: v1.0.0.            *        *      *
// ** *     *       *   *       *   *   *   *     **
// * *  *       *     *      *   *       *  *  * * *

/**
 * general api
 * 
 *  
 */

  // dotenv
import {} from 'dotenv/config';
import { getFullName, getBalances } from '../resources/js/helpers/bots.mjs';

const _launch = async (_name, _scope="paper") => {

  const _bot = {
    name: _name,
    apiKey: process.env["BOTS_"+_name.replaceAll(" ","_")],
    scope: _scope
  }
    return new Promise((resolve, reject) => {
      const check = async () => {
        try {
     		//let result = await getFullName('botName','jawsome');
     		let result = await getBalances(_bot);
        	console.log(result);
        	resolve(result);
        } catch (err) {
        	console.log(err);
          	reject();
        }
      }
      check(); 
    });
} 

_launch("P0ng Doge HFT", "production");