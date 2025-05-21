import { IEventSystem } from "../../ts-common/events";
import { Grid } from "../../ts-grid";
import { Spreadsheet } from "./Spreadsheet";
import { IBufferManager, ISelection, SpreadsheetEvents, SpreadsheetServiceEvents } from "./types";
import { Store } from "./Store";
export interface ISelectedCell {
    row: string;
    col: string;
    cell: string;
    edit?: boolean;
}
export declare class Selection implements ISelection {
    private _spreadsheet;
    private _grid;
    private _store;
    private _bufferManager;
    private _events;
    private _mousePressed;
    private _resizedColumns;
    private _resizedRows;
    private _pressedArea;
    private _cellsToCopy;
    private _searchCell;
    private _searchedCells;
    private _filteredCells;
    private _prevHeaderCss;
    private _serviceEvents;
    private _selectedCols;
    private _selectedRows;
    constructor(spreadsheet: Spreadsheet, grid: Grid, bufferManager: IBufferManager, store: Store, serviceEvents: IEventSystem<SpreadsheetServiceEvents & SpreadsheetEvents>);
    setSelectedCell(cell: string, scroll?: boolean): void;
    getSelectedCell(): string;
    getFocusedCell(): string;
    setFocusedCell(cell: string, scroll?: boolean): void;
    removeSelectedCell(cell?: string): void;
    private _isInRange;
    _setSearchedCell(index: number): void;
    _setSearchedArray(cells: string[]): void;
    _removeSearchCell(): void;
    private _removeHeadersCss;
    private _setHeadersCss;
    private _setCss;
    private _selectRow;
    private _selectColumn;
    private _setGroupSelectionHandlers;
    private _setServiceHandlers;
    private _setHandlers;
}
