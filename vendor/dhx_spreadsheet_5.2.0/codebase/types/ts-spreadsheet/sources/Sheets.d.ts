import { DataCollection } from "../../ts-data";
import { Store } from "./Store";
import { IEventSystem } from "../../ts-common/events";
import { ISheet, ISpreadsheetConfig, Id, SpreadsheetEvents, SpreadsheetServiceEvents } from "./types";
import { Toolbar } from "../../ts-toolbar/sources/entry_pro";
import { ContextMenu } from "../../ts-menu";
interface SheetsConfig {
    store: Store;
    events: IEventSystem<SpreadsheetEvents>;
    serviceEvents: IEventSystem<SpreadsheetServiceEvents>;
    config: ISpreadsheetConfig;
}
export declare class Sheets {
    sheets: DataCollection<{
        id: string;
        value: string;
        prevValue?: string;
    }>;
    tabbar: Toolbar;
    events: IEventSystem<SpreadsheetEvents>;
    contextMenu: ContextMenu;
    private _store;
    private _dataStore;
    private _multiSheets;
    private _focusedSheet;
    private readonly;
    private _errorAlert;
    private _activeSheet;
    private _serviceEvents;
    constructor({ store, events, serviceEvents, config }: SheetsConfig);
    parseSheets(sheets: ISheet[]): void;
    add(name?: string): any;
    remove(id: string): void;
    getAll(): ISheet[];
    getActive(): {
        name: string;
        id: Id;
    };
    clear(id?: Id): void;
    setActive(id: Id): void;
    get(id: Id): ISheet;
    destructor(): void;
    private _generateNewSheetName;
    private _endEdit;
    private _getErrorWindow;
    private _renameSheet;
    _initHandlers(): void;
}
export {};
