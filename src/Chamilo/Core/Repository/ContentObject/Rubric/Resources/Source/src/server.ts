import { Server, Model } from "miragejs"
import Level from "./Domain/Level";
import Rubric from "./Domain/Rubric";
import Cluster from "./Domain/Cluster";
import Category from "./Domain/Category";
import Criterium from "./Domain/Criterium";

export function makeServer({ environment = "development" } = {}) {
    const level1 = new Level("Overstijgt de verwachtingen", "", 10);
    const level2 = new Level("Voldoet aan de verwachtingen", "", 7);
    const level3 = new Level("Voldoet bijna aan de verwachtingen", "", 4);
    const level4 = new Level("Voldoet niet aan de verwachtingen", "", 0);

    let newRubric = new Rubric("Een rubric");
    newRubric.addLevel(level1);
    newRubric.addLevel(level2);
    newRubric.addLevel(level3);
    newRubric.addLevel(level4);

    const cluster1 = new Cluster("Cluster 1");
    cluster1.collapsed = true;
    const category1 = new Category("Professioneel Communiceren");
    cluster1.addCategory(category1);
    category1.color = "#1FBC9C";

    const criterium1 = new Criterium("Volledigheid antwoorden");
    const criterium2 = new Criterium("Onderbouwde mening");
    //const criterium3 = new Criterium("Project stakeholders defined");

    category1.addCriterium(criterium1);
    category1.addCriterium(criterium2);
    //category1.addCriterium(criterium3);
    const category2 = new Category("Categorie 2");
    category2.color = "#2980B9";
    cluster1.addCategory(category2);

    const criteria21 = new Criterium("Volledigheid antwoorden");
    //const criteria22 = new Criterium("Onderbouwde mening");
    const criteria23 = new Criterium("Project stakeholders defined");
    category2.addCriterium(criteria21);
    //category2.addCriterium(criteria22);
    category2.addCriterium(criteria23);

    const cluster2 = new Cluster('Een tweede cluster');
    cluster2.collapsed = true;
    const category3 = new Category('Categorie 3');
    category3.color = '#F39C19';

    category3.addCriterium(new Criterium('Nog een laatste criterium'));

    cluster2.addCategory(category3);

    newRubric.addCluster(cluster1);
    newRubric.addCluster(cluster2);

    newRubric.getChoice(criterium1, level1)!.feedback = "Student geeft steeds volledige en betrouwbare informatie. Alle informatie is opgenomen in de antwoorden.";
    newRubric.getChoice(criterium1, level2)!.feedback = "Student geeft soms volledige en betrouwbare informatie. Niet alle informatie is opgenomen in de antwoorden.";
    newRubric.getChoice(criterium1, level3)!.feedback = "Student geeft zo goed als altijd onvolledige en twijfelachtige informatie die vragen oproept.";
    newRubric.getChoice(criterium1, level4)!.feedback = "Student geeft zijn mening onderbouwd en overtuigend.";
    newRubric.getChoice(criterium2, level1)!.feedback = "Student geeft steeds volledige en betrouwbare informatie. Alle informatie is opgenomen in de antwoorden.";
    newRubric.getChoice(criterium2, level2)!.feedback = "Student geeft steeds volledige en betrouwbare informatie. Alle informatie is opgenomen in de antwoorden.";

    let server = new Server({
        routes() {
            this.namespace = "api";

            this.get("/rubrics", () => {
                return {
                    data: newRubric,
                }
            });

            this.get("/save", () => {
                return {
                    data: "ok!"
                }
            }, { timing: 500 });
        }
    });

    return server
}
