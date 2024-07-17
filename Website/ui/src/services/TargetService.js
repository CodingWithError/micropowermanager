import { Paginator } from '@/Helpers/Paginator'
import { resources } from '@/resources'
import RepositoryFactory from '../repositories/RepositoryFactory'
import { ErrorHandler } from '@/Helpers/ErrorHander'
import { City } from '@/services/CityService'
import { ConnectionsType } from '@/services/ConnectionTypeService'

export class SubTarget {
    constructor() {
        this.id = null
        this.targetId = null
        this.revenue = null
        this.newConnections = null
        this.revenue = null
    }

    fromJson(jsonData) {
        this.id = jsonData.id
        this.targetId = jsonData.target_id
        this.revenue = jsonData.revenue
        this.newConnections = jsonData.new_connections
        this.revenue = jsonData.revenue

        let connectionType = new ConnectionsType()
        this.connections = connectionType.fromJson(jsonData.connection_type)
        return this
    }
}

export class Target {
    constructor() {
        this.id = null
        this.startDate = null
        this.endDate = null
        this.subTargets = []
        this.city = new City()
    }

    fromJson(jsonData) {
        this.id = jsonData.id
        this.targetDate = jsonData.target_date
        this.type = jsonData.type
        this.owner = jsonData.owner

        if ('sub_targets' in jsonData) {
            for (let i = 0; i < jsonData.sub_targets.length; i++) {
                let subTarget = new SubTarget()
                this.subTargets.push(
                    subTarget.fromJson(jsonData.sub_targets[i]),
                )
            }
        }
        return this
    }
}

export class Targets {
    constructor() {
        this.list = []
        this.paginator = new Paginator(resources.target.list)
        this.repository = RepositoryFactory.get('target')
    }

    targetAtIndex(index) {
        return index >= this.list.length ? null : this.list[index]
    }

    async store(period, targetType, targetId, list) {
        let target = {
            period: period,
            targetForType: targetType,
            targetForId: targetId,
            data: list,
        }
        try {
            let response = await this.repository.store(target)
            if (response.status === 201) {
                return response
            } else {
                return new ErrorHandler(response.error, 'http', response.status)
            }
        } catch (e) {
            return new ErrorHandler(e, 'http')
        }
    }

    async updateList(data) {
        this.list = []

        for (let t in data) {
            let target = new Target()
            let owner = null

            target = target.fromJson(data[t])

            owner = data[t].owner_type

            this.list.push({
                target: target,
                owner: owner,
            })
        }
    }
}
