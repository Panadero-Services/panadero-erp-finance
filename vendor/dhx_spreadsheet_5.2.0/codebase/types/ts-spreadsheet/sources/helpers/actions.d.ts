import { IGrid, IGridConfig, IRow } from "../../../ts-grid";
import { DataPage } from "../../../muon";
import { IMerged, IMuonCell, IPageMeta } from "../types";
import { Store } from "../Store";
export declare function getHeaderCell({ letter, index, showMenuIcon, nextColIsHidden, nextHiddenRow }: {
    letter?: string;
    index?: number;
    showMenuIcon?: boolean;
    nextColIsHidden?: boolean;
    nextHiddenRow?: boolean;
}): string;
export declare function getIndexRowCell(row: IRow, meta: IPageMeta): string;
export declare function updateColumns(config: IGridConfig): void;
export declare function updateRowsIndex(data: any): void;
export declare function removeRowsCss(grid: IGrid): void;
export declare function setAutoHeightToRow(cell: string, grid: IGrid, page: DataPage, multiline: string): boolean | undefined;
export declare function updateSpans(dc: number, dr: number, cell: string, page: DataPage, store: Store): {
    prev: IMerged;
    next: IMerged;
    nextBlock: IMuonCell[];
};
