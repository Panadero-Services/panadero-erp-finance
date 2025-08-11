import { IFilter, SpreadsheetServiceEvents } from "../types";
import { DataPage } from "../../../muon";
import { Grid } from "../../../ts-grid";
import { IEventSystem } from "../../../ts-common/events";
import { Store } from "../Store";
export declare class FilterForm {
    protected _handlers: {
        [key: string]: (...args: any[]) => void;
    };
    private _form;
    private _popup;
    private _data;
    private _grid;
    private _ruleIndex;
    private _filter;
    private _checkboxesState;
    private _set;
    private _initValue;
    private _serviceEvents;
    private _controls;
    private _hidden;
    private _store;
    constructor(grid: Grid, set: any, serviceEvents: IEventSystem<SpreadsheetServiceEvents>, store: Store);
    hide(): void;
    show(cell: string, e: MouseEvent, filter: IFilter, page: DataPage): void;
    apply(): void;
    protected _initForm(): void;
    protected _updateStateByCondition(condition: string, val?: any): void;
    applyFilter(page: DataPage): void;
    protected _showRow(id: any): void;
    protected _hideRow(id: any): void;
    protected _updateGridData(filtered: boolean[]): void;
    protected _updateButton(selectAll?: boolean): void;
    protected _setCheckboxes(search?: string): void;
    protected _selectAll(select: any): void;
    protected _initHandlers(): void;
    protected _initServiceHandlers(): void;
}
