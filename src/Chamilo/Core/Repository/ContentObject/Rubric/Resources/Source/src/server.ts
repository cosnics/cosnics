import { Server } from 'miragejs';
import Level from './Domain/Level';
import Rubric from './Domain/Rubric';
import Cluster from './Domain/Cluster';
import Category from './Domain/Category';
import Criterium from './Domain/Criterium';

const d = {
    title: 'Een rubric',
    levels: [
        { name: 'level1', params: ['Overstijgt de verwachtingen', '', 10] },
        { name: 'level2', params: ['Voldoet aan de verwachtingen', '', 7] },
        { name: 'level3', params: ['Voldoet bijna aan de verwachtingen', '', 4] },
        { name: 'level4', params: ['Voldoet niet aan de verwachtingen', '', 0] }
    ],
    clusters: [
        {
            title: 'Een rubric',
            categories: [
                {
                    title: 'Professioneel Communiceren',
                    color: '#00943E',
                    criteria: [
                        {
                            title: 'Volledigheid antwoorden',
                            choices: [
                                { level: 'level1', feedback: 'Student geeft steeds volledige en betrouwbare informatie. Alle informatie is opgenomen in de antwoorden.' },
                                { level: 'level2', feedback: 'Student geeft soms volledige en betrouwbare informatie. Niet alle informatie is opgenomen in de antwoorden.' },
                                { level: 'level3', feedback: 'Student geeft zo goed als altijd onvolledige en twijfelachtige informatie die vragen oproept.' },
                                { level: 'level4', feedback: 'Student geeft zijn mening onderbouwd en overtuigend.' }
                            ]
                        },
                        {
                            title: 'Onderbouwde mening',
                            choices: [
                                { level: 'level1', feedback: 'Student geeft steeds volledige en betrouwbare informatie. Alle informatie is opgenomen in de antwoorden.' },
                                { level: 'level2', feedback: 'Student geeft steeds volledige en betrouwbare informatie. Alle informatie is opgenomen in de antwoorden.' }
                            ]
                        }
                    ]
                },
                {
                    title: 'Categorie 2', color: '#0182ED',
                    criteria: [
                        { title: 'Volledigheid antwoorden', choices: [] },
                        { title: 'Project stakeholders defined', choices: [] }
                    ]
                }
            ]
        },
        {
            title: 'Cluster 1',
            categories: [
                {
                    title: 'Categorie 3', color: '#E76F01',
                    criteria: [
                        { title: 'Nog een laatste criterium', choices: [] }
                    ]
                }
            ]
        },
        { title: 'Een tweede cluster', categories: [ { title: '', color: '', criteria: []}] },
    ]
};

function createDummyRubric(): Rubric {
    const levels: any = {};
    const rubric = new Rubric(d.title);

    d.levels.forEach(({name, params}) => {
        const [title, description, weight] = params;
        levels[name] = new Level(title as string, description as string, weight as number);
        rubric.addLevel(levels[name]);
    });

    d.clusters.forEach(({title, categories}) => {
        const cluster = new Cluster(title as string);
        rubric.addCluster(cluster);

        categories.forEach(({title, color, criteria}) => {
            const category = new Category(title as string);
            category.color = color;
            cluster.addCategory(category);

            criteria.forEach(({title, choices}) => {
                const criterium = new Criterium(title as string);
                category.addCriterium(criterium);

                choices.forEach(({level, feedback}) => {
                    rubric.getChoice(criterium, levels[level])!.feedback = feedback;
                });
            });
        });
    });
    return rubric;
}

export function makeServer({ environment = 'development' } = {}) {
    const newRubric = createDummyRubric();
    
    const server = new Server({
        routes() {
            this.namespace = 'api';

            this.get('/rubrics', () => {
                return {
                    data: newRubric,
                }
            });

            this.get('/save', () => {
                return {
                    data: 'ok!'
                }
            }, { timing: 500 });
        }
    });

    return server;
}
