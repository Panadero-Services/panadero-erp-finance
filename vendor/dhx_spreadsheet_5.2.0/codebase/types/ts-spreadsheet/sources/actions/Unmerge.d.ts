import { IAction, IActionConfig } from "../types";
export declare class Unmerge implements IAction {
    config: IActionConfig;
    private _prevData;
    private _prevSpans;
    constructor(config: IActionConfig);
    do(): void;
    undo(): void;
}
