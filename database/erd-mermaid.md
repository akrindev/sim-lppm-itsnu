# SIM-LPPM ERD - Mermaid Visual Diagram

```mermaid
erDiagram
    Users {
        id PK
        string name
        string email UK
        string password
        timestamp email_verified_at
        string two_factor_secret
        json two_factor_recovery_codes
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    Identities {
        id PK
        id user_id FK
        string profile_picture
        timestamp created_at
        timestamp updated_at
    }

    Proposals {
        id PK
        string title
        id submitter_id FK
        string detailable_type
        id detailable_id FK
        id research_scheme_id FK
        id focus_area_id FK
        id theme_id FK
        id topic_id FK
        id national_priority_id FK
        id cluster_level1_id FK
        id cluster_level2_id FK
        id cluster_level3_id FK
        decimal sbk_value
        integer duration_in_years
        text summary
        enum status
        timestamp created_at
        timestamp updated_at
    }

    Research {
        id PK
        integer final_tkt_target
        longText background
        longText state_of_the_art
        longText methodology
        json roadmap_data
        timestamp created_at
        timestamp updated_at
    }

    CommunityServices {
        id PK
        id partner_id FK
        text partner_issue_summary
        text solution_offered
        timestamp created_at
        timestamp updated_at
    }

    Partners {
        id PK
        string name
        string type
        string address
        timestamp created_at
        timestamp updated_at
    }

    FocusAreas {
        id PK
        string name
        timestamp created_at
        timestamp updated_at
    }

    Themes {
        id PK
        id focus_area_id FK
        string name
        timestamp created_at
        timestamp updated_at
    }

    Topics {
        id PK
        id theme_id FK
        string name
        timestamp created_at
        timestamp updated_at
    }

    ResearchSchemes {
        id PK
        string name
        text description
        timestamp created_at
        timestamp updated_at
    }

    NationalPriorities {
        id PK
        string name
        text description
        timestamp created_at
        timestamp updated_at
    }

    ScienceClusters {
        id PK
        string name
        integer level
        id parent_id FK
        timestamp created_at
        timestamp updated_at
    }

    Keywords {
        id PK
        string name
        timestamp created_at
        timestamp updated_at
    }

    ProposalOutputs {
        id PK
        id proposal_id FK
        integer output_year
        string category
        string type
        string target_status
        timestamp created_at
        timestamp updated_at
    }

    BudgetItems {
        id PK
        id proposal_id FK
        string group
        string component
        text item_description
        integer volume
        decimal unit_price
        decimal total_price
        timestamp created_at
        timestamp updated_at
    }

    ActivitySchedules {
        id PK
        id proposal_id FK
        string activity_name
        integer year
        integer start_month
        integer end_month
        timestamp created_at
        timestamp updated_at
    }

    ResearchStages {
        id PK
        id proposal_id FK
        integer stage_number
        string process_name
        text outputs
        string indicator
        id person_in_charge_id FK
        timestamp created_at
        timestamp updated_at
    }

    %% Pivot Tables
    proposal_user {
        id PK
        id proposal_id FK
        id user_id FK
        enum role
        text tasks
        timestamp created_at
        timestamp updated_at
    }

    proposal_keyword {
        id PK
        id proposal_id FK
        id keyword_id FK
        timestamp created_at
        timestamp updated_at
    }

    %% Relationships
    Users ||--o{ Proposals : submits
    Users ||--o{ proposal_user : team_member
    Users ||--o{ ResearchStages : responsible_for

    Users ||--|| Identities : has

    Proposals ||--o{ ProposalOutputs : produces
    Proposals ||--o{ BudgetItems : budgets
    Proposals ||--o{ ActivitySchedules : schedules
    Proposals ||--o{ ResearchStages : stages

    Proposals }o--|| ResearchSchemes : follows
    Proposals }o--|| FocusAreas : categorized_under
    Proposals }o--|| Themes : themed_as
    Proposals }o--|| Topics : topic_of
    Proposals }o--|| NationalPriorities : prioritizes
    Proposals }o--|| ScienceClusters : "clusters_L1"
    Proposals }o--|| ScienceClusters : "clusters_L2"
    Proposals }o--|| ScienceClusters : "clusters_L3"

    Proposals }o--o{ proposal_user : team_members
    Proposals }o--o{ proposal_keyword : tagged_with

    %% Polymorphic Relationship
    Proposals ||--o| Research : morphs_to
    Proposals ||--o| CommunityServices : morphs_to

    CommunityServices ||--o{ Partners : collaborates_with

    FocusAreas ||--o{ Themes : contains
    FocusAreas ||--o{ Proposals : has_proposals

    Themes ||--o{ Topics : contains
    Themes ||--o{ Proposals : has_proposals
    Themes }o--|| FocusAreas : belongs_to

    Topics ||--o{ Proposals : has_proposals
    Topics }o--|| Themes : belongs_to

    ScienceClusters }o--o| ScienceClusters : parent_child
    proposal_user }o--|| Proposals : belongs_to
    proposal_user }o--|| Users : belongs_to_user
    proposal_keyword }o--|| Proposals : belongs_to
    proposal_keyword }o--|| Keywords : belongs_to
```

## How to View This Diagram

1. **GitHub/GitLab**: Copy and paste into a markdown file - most platforms render Mermaid diagrams automatically
2. **Mermaid Live Editor**: Copy the diagram code to [https://mermaid.live](https://mermaid.live) for interactive viewing
3. **VS Code Extension**: Install "Mermaid Preview" extension to view directly in VS Code

## Legend

- **||--||**: One-to-One relationship
- **||--o{**: One-to-Many relationship (one-to-many)
- **}o--o{**: Many-to-Many relationship
- **}o--||**: Many-to-One relationship (many-to-one)
- **||--o|**: One-to-One-or-Many (polymorphic)

## Color Coding

- 🟦 **Blue**: Core entities (Users, Proposals)
- 🟩 **Green**: Content entities (Research, CommunityServices)
- 🟨 **Yellow**: Reference/Taxonomy entities (FocusArea, Theme, Topic)
- 🟪 **Purple**: System/Pivot tables
- 🟥 **Red**: Supporting entities (BudgetItems, etc.)
