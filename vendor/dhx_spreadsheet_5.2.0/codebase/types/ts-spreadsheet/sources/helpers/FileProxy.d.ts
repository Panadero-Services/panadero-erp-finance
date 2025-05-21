export declare class FileProxy {
    private _xlsxWorker;
    private _modulePath;
    constructor(path: string);
    load(type?: "xlsx" | "json", url?: string): Promise<any>;
    private _getXlsxWorker;
    private _getFile;
}
