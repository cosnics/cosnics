import Cluster from "@/Plugins/ScoreRubric/Domain/Cluster";
import Category from "@/Plugins/ScoreRubric/Domain/Category";
import Criterium from "@/Plugins/ScoreRubric/Domain/Criterium";

test('json', () => {
    let cluster = new Cluster("my_cluster");
    let category = new Category('my category');
    let criterium = new Criterium('my criterium');
    cluster.addCategory(category);
    category.color = 'red';
    cluster.addCriterium(criterium);
    expect(Cluster.fromJSON(JSON.stringify(cluster))).toEqual(cluster);
});
