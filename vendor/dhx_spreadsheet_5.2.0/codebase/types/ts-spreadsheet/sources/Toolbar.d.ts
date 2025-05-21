import { Toolbar } from "../../ts-toolbar";
import { ISpreadsheetConfig, SpreadsheetServiceEvents } from "./types";
import { Store } from "./Store";
import { IEventSystem } from "../../ts-common/events";
interface SpreadsheetToolbarConfig {
    store: Store;
    serviceEvents: IEventSystem<SpreadsheetServiceEvents>;
    config: ISpreadsheetConfig;
    uid: any;
}
export declare class SpreadsheetToolbar {
    toolbar: Toolbar;
    colorpickerTarget: string;
    private _store;
    private _colorpicker;
    private _popup;
    private _silencedColorChange;
    private _activeInput;
    private _suid;
    private _borderPopup;
    private _serviceEvents;
    private _borderToolbar;
    private _borderColor;
    private _borderStyleList;
    private _selectedBorder;
    constructor({ config, store, uid, serviceEvents }: SpreadsheetToolbarConfig);
    updateToolbar(): void;
    initEvent(): void;
    showColorpicker(target: string, e?: any): void;
    showBorderPopup(e?: any): void;
    _listTemplate(item: any): string;
    _applyBorders(id: string): void;
}
export {};
