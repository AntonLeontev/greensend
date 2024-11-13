import { defineStore } from "pinia";
import { reactive } from "vue";

export const useAppStore = defineStore("app", () => {
    const actions = reactive([]);
    const conditions = reactive([]);

    function loadAppData() {
        if (sessionStorage.getItem("appData")) {
            const appData = JSON.parse(sessionStorage.getItem("appData"));

            actions.push(...appData.actions);
            conditions.push(...appData.conditions);
            return;
        }

        axios
            .get(route("api.app-data"))
            .then((response) => {
                actions.push(...response.data.actions);
                conditions.push(...response.data.conditions);

                sessionStorage.setItem(
                    "appData",
                    JSON.stringify(response.data)
                );
            })
            .catch((error) => {
                console.log(error);
            });
    }

    return { actions, conditions, loadAppData };
});
