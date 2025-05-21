import { IFormula } from "../../muon";
import { Store } from "./Store";
import { IEventSystem } from "../../ts-common/events";
import { SpreadsheetServiceEvents } from "./types";
export default class EditPopup {
    private _suggestCallback;
    private _popup;
    private _list;
    private _descriptionLayout;
    private _descriptionPopup;
    private _store;
    private _serviceEvents;
    private _cursorStart;
    private _startTrim;
    private _input;
    private _addParenthesis;
    private _cursorChangeListener;
    constructor(cb: Function, store: Store, serviceEvents: IEventSystem<SpreadsheetServiceEvents>);
    isVisible(): boolean;
    hide(): void;
    destructor(): void;
    navigate(key: "arrowDown" | "arrowUp" | "enter"): boolean;
    show(val: string, input: HTMLInputElement, fm: IFormula, maxHeight?: number, cursor?: number): void;
    showDescription(formula_id: string, el: HTMLInputElement | Element): void;
    hideDescription(): void;
    private _insertSuggest;
    private _getPopup;
    private _handleCursorChange;
}
