import { IActionConfig } from "../types";
import { DataPage } from "../../../muon";
import { Store } from "../Store";
export declare class Merge {
    protected _handlers: {
        [key: string]: (...args: any[]) => void;
    };
    private _callAction;
    private _isLocked;
    private _config;
    private _store;
    constructor(cb: any, store: Store, isLocked: any);
    doAction(cell: string, remove: boolean): void;
    checkBeforeMerge(cell: string, remove: boolean): boolean;
    checkBeforeAction(cell: any, page: DataPage, action_translation: string, conf: IActionConfig): boolean;
}
