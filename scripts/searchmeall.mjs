// * * *     *       *   *       *   *   *   * *** *
// *    *       *     *      *   *       *   *     *
// *   SCRIPT : searchMeAll.js      * *         * 
// *   Location scripts/searchMeAll.js            * * 
// *   Modified :JaWsome.Orbit   *                 * 
// *   Date:    13 sep 2024             *          *
// *   Version: v0.0.1.            *        *      *
// ** *     *       *   *       *   *   *   *     **
// * *  *       *     *      *   *       *  *  * * *

/**
 * general sql routines 
 * 
 *  
 */

  // imports 
import { moduleName, moduleVersion, resolveAllNames } from "panadero-self";


const searchMe = async (_n) => {
    const _allNames = await resolveAllNames(_n);
    console.log("_allNames:",_allNames)
}
searchMe(process.argv.slice(2)[0]);
