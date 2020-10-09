import * as marked from 'marked';
import DOMPurify from 'dompurify';

export function convertRubricData(d: any) {
    function sortFn(v1: any, v2: any) {
        return (v1.sort > v2.sort) ? 1 : -1;
    }

    const data: any = {
        "rubric_data_id": d.id,
        "id": String(d.root_node.id),
        "useScores": d.use_scores,
        "title": d.root_node.title,
        "choices": [],
        "criteria": []
    };
    d.levels.sort(sortFn);
    data.levels = d.levels.map((level: any) => ({
        "id": String(level.id),
        "title": level.title,
        "description": level.description || '',
        "score": level.score,
        "is_default": level.is_default
    }));
    const clusters = (d.root_node.children || []).filter((v: any) => v.type === 'cluster');
    clusters.sort(sortFn);
    data.clusters = clusters.map((c: any) => {
        const cluster: any = {
            "id": String(c.id),
            "title": c.title,
            "criteria": []
        };
        const categories = (c.children || []).filter((v: any) => v.type === 'category');
        categories.sort(sortFn);
        cluster.categories = categories.map((c: any) => {
            const category: any = {
                "id": String(c.id),
                "title": c.title,
                "color": c.color || ''
            };
            const criteria = (c.children || []).filter((v: any) => v.type === 'criterium');
            criteria.sort(sortFn);
            category.criteria = criteria.map((c: any) => {
                const criterium = {
                    "id": String(c.id),
                    "title": c.title,
                    "weight": c.weight
                };
                const choices = c.choices || [];
                choices.sort(sortFn);
                choices.forEach((choice: any) => {
                    data.choices.push({
                        "criterium_id": String(criterium.id),
                        "level_id": String(choice.level.id),
                        "selected": choice.selected,
                        "feedback": choice.feedback || '',
                        "has_fixed_score": choice.has_fixed_score,
                        "fixed_score": choice.fixed_score
                    });
                });
                return criterium;
            });
            return category;
        });
        return cluster;
    });
    return data;
}

export function toMarkdown(rawString: string) {
    return DOMPurify.sanitize(marked(rawString));
}