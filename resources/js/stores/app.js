import { defineStore } from "pinia";
import { reactive } from "vue";

export const useAppStore = defineStore("app", () => {
    const scriptNodes = reactive([]);

    function loadScriptNodes() {
        if (sessionStorage.getItem("scriptNodes")) {
            scriptNodes.push(
                ...JSON.parse(sessionStorage.getItem("scriptNodes"))
            );
            return;
        }

        axios
            .get(route("api.script-nodes"))
            .then((response) => {
                scriptNodes.push(...response.data);

                sessionStorage.setItem(
                    "scriptNodes",
                    JSON.stringify(response.data)
                );
            })
            .catch((error) => {
                console.log(error);
            });
    }

    return { scriptNodes, loadScriptNodes };
});
