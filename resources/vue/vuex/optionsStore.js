import { indexModel as indexOptionModel } from '../models/option.js';

const APP_OPTIONS_KEY = 'app-options';
const APP_OPTIONS_EXPIRY_KEY = 'app-options-expiry';
const EXPIRY_TIME_MS = 24 * 60 * 60 * 1000; // 24 horas en milisegundos

const saveToLocalStorage = (data) => {
    const now = Date.now();
    localStorage.setItem(APP_OPTIONS_KEY, JSON.stringify(data));
    localStorage.setItem(APP_OPTIONS_EXPIRY_KEY, now + EXPIRY_TIME_MS);
};

const loadFromLocalStorage = () => {
    const now = Date.now();
    const expiryTime = localStorage.getItem(APP_OPTIONS_EXPIRY_KEY);

    if (expiryTime && now < expiryTime) {
        const data = localStorage.getItem(APP_OPTIONS_KEY);
        return data ? JSON.parse(data) : null;
    }
    return null;
};

const encodeJSONRecursively = (obj) => {
    Object.keys(obj).forEach(key => {
        if (typeof obj[key] === 'object' && obj[key] !== null) {
            encodeJSONRecursively(obj[key]);
        } else if (typeof obj[key] !== 'string') {
            obj[key] = JSON.stringify(obj[key]);
        }
    });
};

export default {
    namespaced: true,

    state() {
        return {
            options: {},
        };
    },

    getters: {
        getOption: (state) => (key) => {
            const getNestedProperty = (obj, path) => {
                return path.split('.').reduce((acc, part) => {
                    return acc && acc[part] !== undefined ? acc[part] : null;
                }, obj);
            };
            return getNestedProperty(state.options, key);
        },
    },

    mutations: {
        setOptions: (state, options) => {
            let parsedValue = JSON.parse(options.value);
            encodeJSONRecursively(parsedValue);
            state.options = parsedValue;
            saveToLocalStorage(parsedValue);
        }
    },

    actions: {
        optionsSetup: async ({ commit, dispatch }) => {
            dispatch('registerShortcuts');
            const storedOptions = loadFromLocalStorage();
            let resetOptions = new URLSearchParams(window.location.search).get('resetOptions');
            if (storedOptions && !resetOptions) {
                commit('setOptions', { value: JSON.stringify(storedOptions) });
            } else {
                await dispatch('fetchOptions');
            }
        },

        fetchOptions: async ({ commit }) => {
            let res = await indexOptionModel({
                // Puedes añadir filtros aquí si los necesitas
            });
            
            if (res.data.length) {
                // Convertir la respuesta a un super objeto JSON donde la key sea el nombre de la propiedad
                let optionsObj = {};
                
                res.data.forEach(option => {
                    optionsObj[option.key] = option.value;
                });
                
                commit('setOptions', { value: JSON.stringify(optionsObj) });
            }
        },

        registerShortcuts: ({ dispatch }) => {
            
            // Ctrl + Delete: Reset options
            document.addEventListener('keydown', function(event) {
                if (event.ctrlKey && event.key === 'Delete') {
                    let currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('resetOptions', 'true');
                    window.location.href = currentUrl.toString();
                }
            });

            // Shift + Alt + O: Fetch options
            document.addEventListener('keydown', function(event) {
                if (event.shiftKey && event.altKey && event.key === 'O') {
                    dispatch('fetchOptions');
                }
            });
        }
    },
};
