import { DataPage } from "../../../muon";
import { SpreadsheetServiceEvents } from "../types";
import { IEventSystem } from "../../../ts-common/events";
import { Spreadsheet } from "../Spreadsheet";
export declare class SearchForm {
    protected _handlers: {
        [key: string]: (...args: any[]) => void;
    };
    private _toolbar;
    private _modal;
    private _page;
    private _data;
    private _filtered;
    private _stepIndex;
    private _searchText;
    private _events;
    private _spreadsheet;
    private _grid;
    private _serviceEvents;
    constructor(spreadsheet: Spreadsheet, serviceEvents: IEventSystem<SpreadsheetServiceEvents>);
    hide(): void;
    research(page: DataPage): void;
    show(page: DataPage): void;
    isVisible(): boolean;
    search(text: string, page?: DataPage): string[];
    next(): void;
    prev(): void;
    destructor(): void;
    private _updateWindowPos;
    private _keyHandler;
    protected _initHandlers(): void;
}
