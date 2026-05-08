import { useCallback, useState } from 'react';

const toneClass = {
    danger: {
        icon: 'bg-red-50 text-[#C62828]',
        confirm: 'bg-[#C62828] text-white hover:bg-red-800',
    },
    primary: {
        icon: 'bg-blue-50 text-[#265F9C]',
        confirm: 'bg-[#265F9C] text-white hover:bg-blue-700',
    },
};

const useConfirmDialog = () => {
    const [state, setState] = useState(null);

    const confirm = useCallback((options) => new Promise((resolve) => {
        setState({
            tone: 'primary',
            confirmLabel: 'Ya',
            cancelLabel: 'Tidak',
            ...options,
            resolve,
        });
    }), []);

    const close = (answer) => {
        if (state?.resolve) {
            state.resolve(answer);
        }
        setState(null);
    };

    const ConfirmDialog = () => {
        if (!state) return null;

        const classes = toneClass[state.tone] || toneClass.primary;

        return (
            <div className="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
                <div className="w-full max-w-sm rounded-xl bg-white p-5 shadow-2xl">
                    <div className="flex items-start gap-3">
                        <div className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-black ${classes.icon}`}>
                            ?
                        </div>
                        <div className="min-w-0">
                            <h2 className="text-base font-bold text-[#1A1A1A]">{state.title || 'Konfirmasi'}</h2>
                            {state.message && (
                                <p className="mt-1 text-sm leading-6 text-[#585858]">{state.message}</p>
                            )}
                        </div>
                    </div>

                    <div className="mt-5 flex justify-end gap-2">
                        <button
                            type="button"
                            onClick={() => close(false)}
                            className="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition-colors hover:bg-slate-50"
                        >
                            {state.cancelLabel}
                        </button>
                        <button
                            type="button"
                            onClick={() => close(true)}
                            className={`rounded-lg px-4 py-2 text-xs font-bold shadow-sm transition-colors ${classes.confirm}`}
                        >
                            {state.confirmLabel}
                        </button>
                    </div>
                </div>
            </div>
        );
    };

    return { confirm, ConfirmDialog };
};

export default useConfirmDialog;
