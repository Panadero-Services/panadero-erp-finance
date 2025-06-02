// * * *     *       *   *       *   *   *   * *** *
// *    *       *     *      *   *       *   *     *
// *   SCRIPT : testTg.js      * *         * 
// *   Location scripts/testTg.js            * * 
// *   Modified :JaWsome.Orbit   *                 * 
// *   Date:    14 sep 2024             *          *
// *   Version: v0.0.1.            *        *      *
// ** *     *       *   *       *   *   *   *     **
// * *  *       *     *      *   *       *  *  * * *

  // imports 
import {} from 'dotenv/config';
import { moduleName, moduleVersion, testTg } from "panadero-self";

const sendTestMsg = async (_msg) => {
    const _token = process.env['TELEGRAM_KEY'];
    const _titanLegendsBurn = "-1002375022755";
    await testTg(_msg, _token, _titanLegendsBurn);
    console.log("testMsg:",_msg)
    process.exit(0);
}

sendTestMsg(process.argv.slice(2)[0]);