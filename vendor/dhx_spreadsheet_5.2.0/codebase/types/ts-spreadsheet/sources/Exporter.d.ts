import { ISpreadsheet } from "./types";
export declare class Exporter {
    private _spreadsheet;
    private _xlsxWorker;
    constructor(spreadsheet: ISpreadsheet);
    xlsx(name?: string): void;
    json(): void;
    private _getXlsxWorker;
}
