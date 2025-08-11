import { IAction, IActionConfig } from "../types";
export declare class Hide implements IAction {
    config: IActionConfig;
    private _page;
    private _prevCols?;
    private _prevRows?;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
