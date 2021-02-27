export default {
    uiState: {
        builder: {
            showSplitView: false,
            selectedCriterium: '',
            selectedClusterView1: '',
            selectedClusterView2: ''
        },
        preview: {
            showDefaultFeedbackFields: false
        },
        entry: {
            showDefaultFeedbackFields: false,
            options: {
                isDemo: true,
                evaluator: null,
            }
        }
    },
    rubricData: {
        "id": "142",
        "useScores": true,
        "title": "Demo Rubric",
        "levels": [
            {
                "id": "26",
                "title": "Goed",
                "description": "",
                "score": 10,
                "is_default": false
            },
            {
                "id": "28",
                "title": "Matig",
                "description": "",
                "score": 5,
                "is_default": true
            },
            {
                "id": "27",
                "title": "Slecht",
                "description": "",
                "score": 0,
                "is_default": false
            }
        ],
        "clusters": [
            {
                "id": "141",
                "title": "Demo Rubric",
                "categories": [
                    {
                        "id": "140",
                        "title": "",
                        "criteria": [
                            {
                                "id": "158",
                                "title": "Criterium 1",
                                "weight": 100
                            },
                            {
                                "id": "159",
                                "title": "Criterium 2",
                                "weight": 100
                            }
                        ]
                    }
                ],
                "criteria": []
            }
        ],
        "choices": [
            {
                "selected": false,
                "feedback": "",
                "has_fixed_score": false,
                "fixed_score": 0,
                "criterium_id": "158",
                "level_id": "26"
            },
            {
                "selected": false,
                "feedback": "",
                "has_fixed_score": false,
                "fixed_score": 0,
                "criterium_id": "158",
                "level_id": "27"
            },
            {
                "selected": false,
                "feedback": "",
                "has_fixed_score": false,
                "fixed_score": 0,
                "criterium_id": "158",
                "level_id": "28"
            },
            {
                "selected": false,
                "feedback": "",
                "has_fixed_score": false,
                "fixed_score": 0,
                "criterium_id": "159",
                "level_id": "26"
            },
            {
                "selected": false,
                "feedback": "",
                "has_fixed_score": false,
                "fixed_score": 0,
                "criterium_id": "159",
                "level_id": "27"
            },
            {
                "selected": false,
                "feedback": "",
                "has_fixed_score": false,
                "fixed_score": 0,
                "criterium_id": "159",
                "level_id": "28"
            }
        ]
    },
    rubricResults: {
        "evaluators": [
            {"userId": 2, "name": "Bob", "role": "student", "targetUserId": 2, "targetName": "Bob"},
            {"userId": 3, "name": "Alice", "role": "docent", "targetUserId": 2, "targetName": "Bob"}
        ],
        "evaluations": {
            2: [
                { "treeNodeId": "158", "levelId": "28", "feedback": "My feedback"},
                { "treeNodeId": "159", "levelId": "28", "feedback": ""},
                { "treeNodeId": "141", "levelId": null, "feedback": "My comments"}
            ],
            3: [
                { "treeNodeId": "158", "levelId": "28", "feedback": ""},
                { "treeNodeId": "159", "levelId": "28", "feedback": ""}
            ]
        }
    }
};