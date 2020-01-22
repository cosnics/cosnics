import Cluster from "@/../../../../../ScoreRubric/src/Domain/Cluster";
import Category from "@/../../../../../ScoreRubric/src/Domain/Category";
import Criterium from "@/../../../../../ScoreRubric/src/Domain/Criterium";

test('json', () => {
    let cluster = new Cluster("my_cluster");
    let category = new Category('my category');
    let criterium = new Criterium('my criterium');
    cluster.addCategory(category);
    category.color = 'red';
    cluster.addCriterium(criterium);
    expect(Cluster.fromJSON(JSON.stringify(cluster))).toEqual(cluster);
});
