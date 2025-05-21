import { Store } from "../../../muon";
import { Store as IStore } from "../Store";
export declare const alphaRegex: RegExp;
export declare function getCellsDiff(firstCell: string, lastCell: string, store: IStore): {
    x: number;
    y: number;
    isLargerByX: boolean;
    negativeDir: boolean;
};
export declare function getLastCopyingCell(firstCell: string, lastCell: string, store: IStore): string;
export declare function getProgressionStep(progression: any[]): number;
export declare function getDateStep(dates: any[]): number;
export declare function getTimeStep(dates: any[]): number;
export declare function isCellGreater(a: string, b: string, store: IStore): boolean;
export declare function getAutoFilledCells(values: any[], focused: string, selected: string, math: Store, store: IStore): {
    cells: string;
    value: any[];
    spans: any;
};
