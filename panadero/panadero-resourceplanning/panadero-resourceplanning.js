// * * *     *       *   *       *   *   *   * *** *
// *    *       *     *      *   *       *   *     *
// *   API : panadero-resourceplanning      * *       * 
// *   Location /modules/panadero-resourceplanning  * * 
// *   Modified :JaWsome.Orbit   *                 * 
// *   Date:    12 apr 2025             *          *
// *   Version: v0.1.3             *        *      *
// ** *     *       *   *       *   *   *   *     **
// * *  *       *     *      *   *       *  *  * * *

 //impogantt } from 'dhgantt';
// import "/vendorggantt_9.0.7/codebase/dhgantt.css";

const moduleName = "Panadero-ResourcePlanning";
const moduleGit = "https://github.com/lieuwebakker/panadero-resourceplanning";
const moduleVersion = "0.1.3";
const moduleDate = "12 apr 2022";
const moduleAuthor = "JaWsome.Orbit";
const moduleTitle = "Resource Planning Organized.! modular style!";

class panaderoResourcePlanning {
 constructor(_code="code") {
  this.code = _code;
  this.name = moduleName;
  this.git = moduleGit;
  this.date = moduleDate;
  this.version = moduleVersion;
  this.author = moduleAuthor;
  this.title = moduleTitle;
  this.colors = ['red','gray','white','yellow','magenta','green','blue','cyan','purple','teal'];

  this.resourceData = [];
  this.links = [];
  this.ppl = [];
}


}

export { moduleName, moduleVersion, moduleGit, panaderoResourcePlanning }
