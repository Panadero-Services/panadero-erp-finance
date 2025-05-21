import { DataPage, DataStore, type Store as IStore } from "../../muon";
import { ICellMeta, IPageMeta, IRangeIndex } from "./types";
export declare class Store {
    dataStore: DataStore;
    activePage: DataPage;
    math: IStore;
    constructor();
    getValue(cell: string, formula?: boolean, page?: DataPage): any[] | import("../../muon/dist/types/types").cellValue;
    getCell(cell: string, page?: DataPage): ICellMeta;
    getPage(name: string): DataPage;
    getPageMeta(page?: DataPage): IPageMeta;
    setActivePage(name: string): void;
    getSelectedCell(page?: DataPage): any;
    getFocusedCell(page?: DataPage): string;
    serialize(): void;
    eachCell(cb: any, range: string): void;
    getCellIndex(cell: string, includeHidden?: boolean): {
        col: number;
        row: number;
    };
    getRangeIndexes(range: string, includeHidden?: boolean): IRangeIndex;
    private _findCellIndexWithHiddenRows;
    private _getPageAndCell;
}
