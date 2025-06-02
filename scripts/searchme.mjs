// * * *     *       *   *       *   *   *   * *** *
// *    *       *     *      *   *       *   *     *
// *   SCRIPT : searchMe.js      * *         * 
// *   Location scripts/searchMe.js            * * 
// *   Modified :JaWsome.Orbit   *                 * 
// *   Date:    11 sep 2024             *          *
// *   Version: v0.0.2.            *        *      *
// ** *     *       *   *       *   *   *   *     **
// * *  *       *     *      *   *       *  *  * * *

/**
 * general sql routines 
 * branch JaWsome
 *  
 */

  // imports 
import { moduleName, moduleVersion, resolveName } from "panadero-self";


const searchMe = async (_n) => {
    const _wallet = await resolveName(_n);
    console.log("_wallet:",_wallet)
}
searchMe(process.argv.slice(2)[0]);