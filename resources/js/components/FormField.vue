<script>
    export default {
        data: () => ({
            dependsOnValue: null
        }),
        created() {
            if(this.field.dependsOn !== undefined) {
                this.registerDependencyWatchers(this.$root)
            }
        },
        methods: {
            registerDependencyWatchers(root) {
                root.$children.forEach(component => {
                    if (this.componentIsDependency(component)) {
                        if( component.selectedResourceId !== undefined ) {
                            // BelongsTo field
                            component.$watch('selectedResourceId', this.dependencyWatcher, {immediate: true})
                            this.dependencyWatcher(component.selectedResourceId)
                        }
                        else if( component.value !== undefined ) {
                            // Text based fields
                            component.$watch('value', this.dependencyWatcher, {immediate: true})
                            this.dependencyWatcher(component.value)
                        }
                    }
                    this.registerDependencyWatchers(component)
                })
            },

            componentIsDependency(component) {
                if(component.field === undefined) return false
                return component.field.attribute === this.field.dependsOn
            },
            dependencyWatcher(value) {
                if(value === this.dependsOnValue) return;
                this.dependsOnValue = value
                this.clearSelection()
                this.initializeComponent()
            }
        },


        computed: {
            queryParams() {
                return {
                    params: {
                        current: this.selectedResourceId,
                        first: this.initializingWithExistingResource,
                        search: this.search,
                        withTrashed: this.withTrashed,

                        resourceId: this.resourceId,
                        viaResource: this.viaResource,
                        viaResourceId: this.viaResourceId,
                        viaRelationship: this.viaRelationship,

                        // [resourceName] belongsTo [belongsToRelationship] morphToMany [morphToManyRelationship]

                        // 关联上级的值
                        dependsOnValue: this.dependsOnValue,
                        // 当前的资源
                        resourceName: this.resourceName,
                        // 当前资源 belongsToRelationship
                        belongsToRelationship: this.belongsToRelationship,
                        // belongsToRelationship => morphToManyRelationship
                        morphToManyRelationship: this.field.resourceName
                    },
                }
            },
        }
    }
</script>
