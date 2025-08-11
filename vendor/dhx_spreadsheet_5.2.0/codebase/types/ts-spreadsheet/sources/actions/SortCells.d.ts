import { IAction, IActionConfig } from "../types";
export declare class SortCells implements IAction {
    config: IActionConfig;
    private _data;
    private _rows;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
