import { Store as IStore } from "../Store";
import { IBufferStruct, IExecuteConfig, ISpreadsheet, SpreadsheetEvents, SpreadsheetServiceEvents } from "./../types";
import { IEventSystem } from "../../../ts-common/events";
export declare class BufferManager {
    private _buffer;
    private _spreadsheet;
    private _callAction;
    private _math;
    private _dataStore;
    private _prevCopyPage;
    private _store;
    private _serviceEvents;
    constructor(spreadsheet: ISpreadsheet, callAction: (config: IExecuteConfig) => void, store: IStore, serviceEvents: IEventSystem<SpreadsheetServiceEvents & SpreadsheetEvents>);
    store(operation: IBufferStruct["operation"]): void;
    paste(): void;
    getStruct(): IBufferStruct;
}
